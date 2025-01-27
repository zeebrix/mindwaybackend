<?php

namespace App\Services;

use App\Repositories\DriversRepository;

class DriverService {

    /**
     * @var DriversRepository
     */
    protected $repository;

    public function __construct(DriversRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function getAll(array $filters = []): mixed {
        return $this->repository->getAll($filters);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function store(array $modelValues = []) {
        $modelValues["verification_code"] = $this->repository->getUniqueValue(6, 'verification_code');
        $modelValues['password'] = \Hash::make($modelValues['password']);
        \Arr::forget($modelValues, ["password_confirmation"]);

        $driver = $this->repository->store($modelValues);
        return response()->json([
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'Driver registered successfully.',
                    'data' => [
                        "verification_code" => $driver->verification_code
                    ]], 200);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function updateProfile($request) {
        $input = $modelValues = $request->all();
        //Image Uploading
        if ($request->hasFile('image')) {
            $modelValues["image"] = $request->file('image')->storeAs('drivers', request()->file('image')->getClientOriginalName());
        }

        $driverID = $request["driver_id"];
        \Arr::forget($modelValues, ["driver_id", "driver"]);
        if ($this->repository->update($modelValues, $driverID)) {
            $driver = $this->repository->getOne($driverID);
            $driver = $driver->toArray();
            $driver["bearer_token"] = $driver["api_auth_token"] ?? NULL;
            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Profile updated successfully.',
                        'data' => [$driver]], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Profile unable to update. Something went wrong.'], 421);
    }

    public function verifySignup(array $modelValues = []) {
        $driver = $this->repository->getOne($modelValues["phone"], "phone");

        if ($driver && !$driver->verified_at && $driver->verification_code == $modelValues["verification_code"]) {
            $apiAuthToken = $this->repository->getUniqueValue(10, 'api_auth_token');
            $driver->verified_at = \Carbon\Carbon::now();
            $driver->status = TRUE;
            $driver->api_auth_token = $apiAuthToken;
            $driver->save();

            $driver = $driver->toArray();
            $driver["bearer_token"] = $driver["api_auth_token"] ?? NULL;
            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'driver verified successfully.',
                        'data' => [$driver]], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Phone number or verification code is invalid.'], 421);
    }

    public function login(array $modelValues = []) {
        if (\Auth::guard('driverapi')->attempt(['phone' => request('phone'), 'password' => request('password')])) {
            $apiAuthToken = $this->repository->getUniqueValue(10, 'api_auth_token');
            $driver = \Auth::guard('driverapi')->user();
//            $token = $driver->createToken('MyApp')->accessToken;
            $driver->api_auth_token = $apiAuthToken;
            $driver->save();
            $driver = $driver->toArray();
            $driver["bearer_token"] = $driver["api_auth_token"] ?? NULL;
            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Login successfully.',
                        'data' => [$driver]], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Phone number or password is incorrect.'], 421);
    }

    public function logout(array $modelValues = []) {
        $update = array("api_auth_token" => NULL);
        if ($this->repository->update($update, $modelValues["driver_id"])) {
            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Logout successfully.'], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Something went wrong.'], 421);
    }

    public function forgetPassword(array $modelValues = []) {
        $driver = $this->repository->getOne($modelValues["phone"], "phone");

        if ($driver) {
            $driver->verified_at = NULL;
            $driver-> verification_code= NULL;
            if (empty($driver->verification_code)) {
                $driver->verification_code = $this->repository->getUniqueValue(6, 'verification_code');
            }

            $driver->save();

            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Verification code sent successfully.',
                        'data' => [
                            "verification_code" => $driver->verification_code
                        ]], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Unable to reset your password.'], 421);
    }

    public function resetPassword(array $modelValues = []) {
        $driver = $this->repository->getOne($modelValues["phone"], "phone");

        if ($driver && !$driver->verified_at && $driver->verification_code == $modelValues["verification_code"]) {
            $driver->verified_at = \Carbon\Carbon::now();
            $driver->status = TRUE;
            $driver->password = \Hash::make($modelValues['password']);
            $driver->save();

            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Password reset successfully.'], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Phone number or verification code is invalid.'], 421);
    }

}

 <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Employee To EAP</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="dataForm" method="POST" action="{{ route('saveData') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" style="border-radius: 20px;background-color: #FAFAFA;" id="name" name="name"
                                placeholder="Employee Full Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" style="border-radius: 20px;background-color: #FAFAFA;" id="email" name="email"
                                placeholder="Employee Email Address" required>
                        </div>
                        <button type="submit" class="btn btn-primary mindway-btn">Add Individual</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

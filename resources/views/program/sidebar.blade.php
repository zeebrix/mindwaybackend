<style>
 .button-container {
    display: flex;
    align-items: center; /* Align items vertically */
}

#bluebutton {
    background-color: #688EDC;
    color: white;
    border-top-right-radius: 30px;
    border-bottom-right-radius: 30px;
    border: none;
    width: 50px;
    height: 130px;
}
.blue-button {
    background-color: #688EDC;
    color: white;
    border-top-right-radius: 30px;
    border-bottom-right-radius: 30px;
    border: none;
    width: 55px;
    height: 134px;
}
.heading-container {
    margin-left: 10px; /* Add space between button and headings */
}

.container {
    text-align: center; /* Center align content */
}

.rounded-button {
    background-color: #688EDC;
    color: white;
    border: none;
    border-radius: 20px; /* Add rounded corners */
    padding: 10px 20px; /* Add padding */
    margin-top: 20px; /* Add space between h1 and button */
}


</style>
@php
// Get the authenticated user's ID
use App\Models\Program;
use Illuminate\Support\Facades\Auth;
 // Get the authenticated user's ID
 $userId = Auth::guard('programs')->id();

// Get programs associated with the user
$programs = Program::where('id', $userId)->get();
        @endphp
<nav class="sidebar sidebar-offcanvas" id="sidebar" style=" border-top-right-radius: 20px; width:280px; margin-left:-25px">
<div class="container" style="margin-bottom: 20px; margin-top: 40px;">
    <!-- <img src="{{ asset('logo/icon.png') }}" alt="Icon" style="width: 70px; height: 50px; margin-right: 10px; float: left; margin-bottom: 5px; margin-left:20px"> -->
    <img src="{{ asset('/logo/blue2.png') }}" alt="logo"   style="width: 70px; height: 50px; margin-right: 10px; float: left; margin-bottom: 5px; margin-left:20px">
    <div style="overflow: hidden;"> <!-- Adding a wrapper to contain floated image -->
        <h1 style="font-size: 27px; font-weight: 800; font-family: Arial, Helvetica, sans-serif; margin-right: 30px; color: black; ">Mindway</h1> <!-- Adjusted margin-bottom -->
        <h1 style="font-family: Arial, Helvetica, sans-serif; margin: 0; margin-bottom: 0; font-size: 20px; font-weight: 600; color: #688EDC; margin-right: 30px; margin-top: -10px;">For  @foreach ($programs as $program)
    {{ $program->company_name }}
@endforeach
</h1> <!-- Removed margin-bottom -->
    </div>
</div>
<hr style="clear: both; width: 80%; margin: 0 auto;">







<div class="container" style=" margin-top: 15px;">
<h1  style="font-family: Arial, Helvetica, sans-serif; margin: 0; margin-bottom: 5px; font-size: 25px; font-weight: 600; color: #688EDC;">
 @foreach ($programs as $program)
    {{ $program->code }}
</h1>
<h3 style="  font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    margin-bottom: 5px; font-size:20px; font-weight:500">Organisation Access Code
@endforeach</h3>
</div>
<hr style="width: 80%; margin: 0 auto;">


<div class="container" style=" margin-top: 25px;">
    <h1 style=" font-size: 20px;
    font-weight: 800;
    font-family: Arial, Helvetica, sans-serif;
    margin: 0; color:black; margin-bottom:10px ">How staff can use EAP</h1>
</div>

<div class="button-container">

    <button id="bluebutton"><h3>1</h3></button>
    <div class="heading-container">
    <h1 style=" font-size: 18px;
    font-weight: 800;
    font-family: Arial, Helvetica, sans-serif;
    margin: 0; color:black; white-space: nowrap;">Install Mindway App</h1>
    <h3 style="  font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    margin-bottom: 5px; font-size:20px; font-weight:500">Search Mindway on </h3>
    <h3 style="  font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    margin-bottom: 5px; font-size:20px; font-weight:500;">app stores or...</h3>
  <a  style="font-family: Arial, Helvetica, sans-serif; margin: 0; margin-bottom: 5px; font-size: 20px; font-weight: 500; color: #688EDC;" target="_blank" href="https://mindwayeap.com.au/setup">
    Copy Link To Share
    <i class="fa fa-link" aria-hidden="true" style="margin-left: 2px;"></i> <!-- Adjust margin-left for space -->
</a>

</div>
</div>
<!-- second secction -->
<div class="button-container" style="margin-top:15px">
    <button class="blue-button"><h3>2</h3></button>
    <div class="heading-container">
    <h1 style=" font-size: 18px;
    font-weight: 800;
    font-family: Arial, Helvetica, sans-serif;
    margin: 0; color:black;">Book A Counselling Session</h1>
   <a  style="font-family: Arial, Helvetica, sans-serif; margin: 0; margin-bottom: 5px; font-size: 20px; font-weight: 500; color: #688EDC;" target="_blank" href="https://mindwayeap.com.au/booking">
    Copy Link To Share
    <i class="fa fa-link" aria-hidden="true" style="margin-left: 2px;"></i> <!-- Adjust margin-left for space -->
</a>

</div>
</div>
<div class="container">
    <h1 style=" font-size: 20px;
    font-weight: 800;
    font-family: Arial, Helvetica, sans-serif;
    margin: 0; color:black; ">Need A Guide To Join EAP?</h1>
<button class="rounded-button" onclick="redirectToGuide()">See Guide</button>


</div>



</nav>
<script>
document.getElementById("copyLink").addEventListener("click", function(event) {
    event.preventDefault(); // Prevent the default behavior of the link

    // Get the href value of the anchor element
    var hrefValue = this.getAttribute("href");

    // Copy the href value to the clipboard
    navigator.clipboard.writeText(hrefValue).then(function() {
        console.log("Href value copied successfully!");
        alert("Href value copied successfully!");
    }, function() {
        console.error("Failed to copy href value!");
    });
});

function redirectToGuide() {
    window.open('https://mindwayeap.com.au/setup', '_blank');

    }
</script>

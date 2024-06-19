<style>
    .nav_logo-logo {
        height: 70px;
        width: 70px;
        text-align: center;
        font-size: 50px;
        color: white;
        background-color: #1672AE;
        padding-top: 10px;
    }

    .nav_logo-icon {
        height: 70px;
        width: 70px;
        text-align: center;
        font-size: 50px;
        color: black;
        padding-top: 10px;
        background-color: white;
    }

    .nav-items {
        height: 70px;
    }
    
    #nav-bar{
        transition: left 1s ease;
    }

</style>
<div class="l-navbar d-flex align-items-start" id="nav-bar">
    <nav class="nav height-100" id="navMain" style="width: 70px">
        <a href="#"> <i class='bx bx-layer nav_logo-logo'></i></a>
        <a href="#"> <i class='bx bx-file nav_logo-icon'></i></a>
        <a href="#"> <i class='bx bx-camera nav_logo-icon'></i></a>
    </nav>
    <nav id="navBodyItems" class="nav height-100 d-none" style="width: 250px">
        <a href="#">Content</a>
    </nav>
</div>
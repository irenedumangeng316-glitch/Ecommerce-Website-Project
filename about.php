<?php 
    include 'connection.php';
    session_start();
    $user_id = $_SESSION['user_id'];
    if (!isset($user_id)) {
        header('location:login.php');
    }
    if (isset($_POST['Logout'])){
        session_destroy();
        header('location:login.php');
    }

?>

hello
<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!----------------bootstrap icon link------------------->
    <link rel="stylesheet" href="https://cdn.jsdeliver.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="main.css">
    <title>Chalicet - Home Page</title>
</head>

<body>
    <?php include 'header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>About Us</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
            <a href="index.php">Home</a><span>/ About Us</span>
        </div>
    </div>
    <div class="line"></div>
    <!----------------about us------------------->
    <div class="line2"></div>
    <div class="about-us">
        <div class="row">
            <div class="box">
                <div class="title">
                    <span>ABOUT OUR STORE</span>
                    <h1>Each Aura Tumbler is more than just stainless steel;</h1>
                </div>
                <p>In a world filled with single-use plastic and fleeting trends, 
                    a simple idea sparked our journey: what if every sip could carry a story, not just a drink?
                    It began with our founder, Elara, a dedicated commuter frustrated by lukewarm coffee 
                    and the mounting pile of disposable cups. She sought a companion that was as reliable 
                    as the morning sun and as kind to the Earth as a forest stream. 
                    That search led to the creation of the Aura Tumbler.</p>
            </div>
            <div class="img-box">
                <img src="img/Chalicet logo.png">
            </div>
        </div>
    </div>
    <div class="line3"></div>
    <!----------------features------------------->
    <div class="line4"></div>
    <div class="features">
        <div class="title">
            <h1>Complete Customer Ideas</h1>
            <span>Best features</span>
        </div>
        <div class="row">
            <div class="box">
                <img src="img/Chalicet logo.png">
                <h4>24 hours, 7 days a week</h4>
                <p>Online Support 24/7</p>
            </div>
            <div class="box">
                <img src="img/Chalicet logo.png">
                <h4>Money Back Guarantee</h4>
                <p>100% Secure Payment</p>
            </div>
            <div class="box">
                <img src="img/Chalicet logo.png">
                <h4>Special Gift Card</h4>
                <p>Give the Perfect Gift!</p>
            </div>
            <div class="box">
                <img src="img/Chalicet logo.png">
                <h4>Nationwide Shipping</h4>
                <p>On Orders Over 999php</p>
            </div>
        </div>
    </div>
    <div class="line"></div>
    <!----------------features------------------->
    <div class="line2"></div>
    <div class="team">
        <div class="title">
            <h1>Our Team</h1>
            <span>Best Team</span>
        </div>
        <div class="row">
            <div class="box">
                <div class="img-box">
                    <img src="img/Chalicet logo.png">
                </div>
                <div class="detail">
                    <span>Finance Manager</span>
                    <h4>Juan Dela Cruz</h4>
                    <div class="icons">
                        <i class="bi bi-instagram"></i>
                        <i class="bi bi-youtube"></i>
                        <i class="bi bi-twitter"></i>
                        <i class="bi bi-facebook"></i>
                        <i class="bi bi-whatsapp"></i>
                    </div>
                </div>
            </div>
            <div class="box">
                <div class="img-box">
                    <img src="img/Chalicet logo.png">
                </div>
                <div class="detail">
                    <span>Finance Manager</span>
                    <h4>Juan Dela Cruz</h4>
                    <div class="icons">
                        <i class="bi bi-instagram"></i>
                        <i class="bi bi-youtube"></i>
                        <i class="bi bi-twitter"></i>
                        <i class="bi bi-facebook"></i>
                        <i class="bi bi-whatsapp"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="line3"></div>
    <div class="line4"></div>
    <div class="project">
        <div class="title">
            <h1>Our Projects</h1>
            <span>How it Works</span>
        </div>
        <div class="row">
            <div class="box">
                <img src="img/Chalicet logo.png">
            </div>
            <div class="box">
                <img src="img/Chalicet logo.png">
            </div>
        </div>
    </div>
    <div class="line"></div>
    <div class="line2"></div>
    <div class="ideas">
        <div class="title">
            <h1>We and Our Clients' Cooperation</h1>
            <span>Our Features</span>
        </div>
        <div class="row">
            <div class="box">
                <i class="bi bi-stack"></i>
                <div class="detail">
                    <h2>What We Really Do</h2>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. 
                        Amet autem, aliquid dolore praesentium quis corrupti iure! 
                        Inventore repudiandae quisquam porro, maxime totam placeat velit accusamus! 
                        Amet esse quos commodi neque.</p>
                </div>
            </div>
            <div class="box">
                <i class="bi bi-grid-1x2-fill"></i>
                <div class="detail">
                    <h2>Our Beginning</h2>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. 
                        Amet autem, aliquid dolore praesentium quis corrupti iure! 
                        Inventore repudiandae quisquam porro, maxime totam placeat velit accusamus! 
                        Amet esse quos commodi neque.</p>                    
               </div>
            </div>
            <div class="box">
                <i class="bi bi-tropical-storm"></i>
                <div class="detail">
                    <h2>Our Vision</h2>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. 
                        Amet autem, aliquid dolore praesentium quis corrupti iure! 
                        Inventore repudiandae quisquam porro, maxime totam placeat velit accusamus! 
                        Amet esse quos commodi neque.</p>
                    
                </div>
            </div>
        </div>
    </div>
    <div class="line3"></div>
    <?php include 'footer.php'; ?>
    <script type="text/javascript" src="script.js"></script>
</body>

</html>
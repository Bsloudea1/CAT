<?php
@include 'config.php';

session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_name'])) {
    // Retrieve the session variables
    $user_name = $_SESSION['user_name'];
    $user_email = $_SESSION['user_email'];
    $user_profile = $_SESSION['user_profile'];
    $user_type = $_SESSION['user_type'];
} else {
    // If not logged in, assign default values or show a guest message
    $user_name = 'Guest';
    $user_email = 'guest@example.com';
    $user_profile = 'default_profile_pic.png';
    $user_type = ''; // No user type if not logged in
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin page</title>
    <link rel="stylesheet" href="THESIS.CSS">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="img/C.A.T LOGO.png" type="logo">
</head>
<body>
    <div class="sidebar">
        <div class="top">
            <div class="logo">
                    <img src="#" alt="C.A.T LOGO" class="catlogo">
                <span>MENU</span>
            </div>
            <i class="fa-solid fa-bars" id="btn"></i>
        </div>
        <div class="profile">
            <img src="<?php echo isset($_SESSION['user_profile']) ? $_SESSION['user_profile'] : 'default_profile_pic.png'; ?>" 
                alt="Profile Picture">
            <p><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User Name'; ?></p>
        </div>
        <ul>
            <li>
                <a href="admin_page.php">
                    <i class="fa-solid fa-house-chimney"></i>
                    <span class="nav-item">Home</span>
                </a>
                <span class="tooltip">Home</span>
            </li>
            <li>
                <a href="admin_documents.php">
                    <i class="fa-solid fa-folder-open"></i>
                    <span class="nav-item">Document</span>
                </a>
                <span class="tooltip">Document</span>
            </li>
            <li>
                <a href="settings.php" class="btn">
                    <i class="fa-solid fa-gear"></i>
                    <span class="nav-item">Setting</span>
                </a>
                <span class="tooltip">Setting</span>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="nav-item">Logout</span>
                </a>
                <span class="tooltip">Logout</span>
            </li>
        </ul>
    </div>

    <div class="user-main-content">
        <div class="user-container">
            <div class="slideshow">
                <img src="img/RAIDEN HAPPY.jpg" alt="Image 1" class="slide active">
                <img src="img/ptc background.jpg" alt="Image 2" class="slide">
                <img src="img/C.A.T LOGO.png" alt="Image 3" class="slide">
                <img src="" alt="Image 4" class="slide">
                <img src="" alt="Image 5" class="slide">
                <div class="slideshow-controls">
                    <button id="prev" class="control-btn" aria-label="Previous slide">❮</button>
                    <button id="next" class="control-btn" aria-label="Next slide">❯</button>
                </div>
                <div class="dots-container" id="dots-container"></div>
            </div>
            <div class="user-content">
                <h1>Welcome to the Admin Page</h1>
                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Dolorum provident quibusdam quasi tenetur praesentium eaque voluptas aliquam quia voluptate molestias, eum hic molestiae nihil sequi aut, incidunt eligendi enim saepe.</p>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Exercitationem, eum itaque amet dolore ipsam sit quo iste architecto cumque asperiores quam id, consequuntur sunt voluptate excepturi laborum deserunt. Veritatis, minus.</p>
                <p>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Nam eos rem repellendus ducimus accusamus voluptatem magni maxime. Esse corrupti culpa doloremque sint tenetur, voluptatem, ab, iure autem dicta non eum.</p>
            </div>
        </div>
    </div>
</body>

<script>
        let btn = document.querySelector('#btn');
        let sidebar = document.querySelector('.sidebar');

        btn.onclick = function () {
            sidebar.classList.toggle('active');
        };


        let slideIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const dotsContainer = document.getElementById('dots-container');

        function createDots() {
            slides.forEach((slide, index) => {
                const dot = document.createElement('span');
                dot.classList.add('dot');
                dot.addEventListener('click', () => goToSlide(index));
                dotsContainer.appendChild(dot);
            });
        }

        function goToSlide(index) {
            slideIndex = index;
            updateSlide();
        }

        function updateSlide() {
            slides.forEach((slide, index) => {
                slide.classList.remove('active');
                dotsContainer.children[index].classList.remove('active');
            });
            slides[slideIndex].classList.add('active');
            dotsContainer.children[slideIndex].classList.add('active');
        }

        document.getElementById('prev').addEventListener('click', () => {
            slideIndex = (slideIndex - 1 + slides.length) % slides.length;
            updateSlide();
        });

        document.getElementById('next').addEventListener('click', () => {
            slideIndex = (slideIndex + 1) % slides.length;
            updateSlide();
        });

        // Initialize
        createDots();
        updateSlide();

        // Auto slide every 5 seconds (5000ms)
        setInterval(() => {
            slideIndex = (slideIndex + 1) % slides.length;
            updateSlide();
        }, 5000);
</script>
</html>
<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/style.css">

    <title>SarkarSetu AI</title>
</head>

<body>

    <header>

        <h1>SarkarSetu AI</h1>

        <nav>
            <a href="#">Home</a>
            <a href="schemes.php">Schemes</a>
            <a href="#">PIB Updates</a>
            <?php if(isset($_SESSION['user_id'])){ ?>

    <a href="auth/dashboard.php">
        Dashboard
    </a>

    <a href="auth/logout.php">
        Logout
    </a>

<?php }else{ ?>

    <a href="auth/login.php">
        Login
    </a>

    <a href="auth/signup.php">
        Signup
    </a>

<?php } ?>
        </nav>

    </header>

    <main>

        <section class="hero">

    <div class="hero-text">

        <h2>
            Discover Government Schemes Easily
        </h2>

        <p>
            SarkarSetu AI helps citizens and UPSC aspirants understand
            government schemes in simple language with personalized recommendations.
        </p>

        <a href="schemes.php">
    <button>
        Explore Schemes
    </button>
</a>

    </div>

    <div class="hero-image">

        <img 
            src="https://cdn-icons-png.flaticon.com/512/1048/1048953.png"
            alt="Government Schemes"
        >

    </div>

</section>
<section class="categories">

    <h2 class="section-title">
        Explore By Category
    </h2>

    <div class="category-grid">

        <div class="category-card">
            🎓
            <h3>Education</h3>
        </div>

        <div class="category-card">
            🌾
            <h3>Farmers</h3>
        </div>

        <div class="category-card">
            🏥
            <h3>Healthcare</h3>
        </div>

        <div class="category-card">
            👩
            <h3>Women</h3>
        </div>

        <div class="category-card">
            💼
            <h3>Employment</h3>
        </div>

        <div class="category-card">
            🚀
            <h3>Startups</h3>
        </div>

    </div>

</section>

<section class="search-section">

    <h2 class="section-title">
        Find Schemes
    </h2>

    <form class="search-box" action="schemes.php" method="GET">

        <div class="search-input-wrapper">

            <input 
                type="text"
                id="searchInput"
                name="search"
                placeholder="Search schemes, ministries, categories..."
                autocomplete="off"
            >

            <div id="suggestions"></div>

        </div>

        <button type="submit">
            Search
        </button>

    </form>

</section>

<section class="ai-section">

    <div class="ai-box">

        <h2 class="section-title">
            Ask SarkarSetu AI
        </h2>

        <p>
            Example:
            "I am a college student from Goa. What scholarships can I apply for?"
        </p>

        <div class="ai-input-box">

            <input 
                type="text"
                placeholder="Ask about schemes, eligibility, PIB updates..."
            >

            <button>
                Ask AI
            </button>

        </div>

    </div>

</section>

<section class="updates-section">

    <h2 class="section-title">
        Latest Government Updates
    </h2>

    <div class="updates-grid">

        <div class="update-card">

            <span class="update-tag">
                PIB
            </span>

            <h3>
                New Skill Development Initiative Announced
            </h3>

            <p>
                Government launches AI-focused skilling program for students and young professionals.
            </p>

        </div>

        <div class="update-card">

            <span class="update-tag">
                Agriculture
            </span>

            <h3>
                Farmer Subsidy Applications Open
            </h3>

            <p>
                Farmers can now apply online for equipment and irrigation support schemes.
            </p>

        </div>

        <div class="update-card">

            <span class="update-tag">
                Education
            </span>

            <h3>
                Scholarship Portal Updated
            </h3>

            <p>
                New central scholarships added for undergraduate and postgraduate students.
            </p>

        </div>

    </div>

</section>

        <section class="features-section">

    <h2 class="section-title">
        What Does SarkarSetu Do?
    </h2>

    <div class="features">

            <div class="card">

                <h3>Smart Recommendations</h3>

                <p>
                    Get schemes suggested according to your age,
                    occupation, income, and profile.
                </p>

            </div>

            <div class="card">

                <h3>Simple Explanations</h3>

                <p>
                    Understand complicated government policies
                    in easy language.
                </p>

            </div>

            <div class="card">

                <h3>PIB Updates</h3>

                <p>
                    Stay updated with the latest government
                    announcements and policy changes.
                </p>

            </div>

        </section>

    </main>


    <script>

const input = document.getElementById("searchInput");
const suggestions = document.getElementById("suggestions");

input.addEventListener("input", function(){

    const query = input.value;

    // empty input
    if(query.length === 0){
        suggestions.innerHTML = "";
        suggestions.style.display = "none";
        return;
    }

    fetch("suggestions.php?q=" + query)

    .then(response => response.json())

    .then(data => {

        suggestions.innerHTML = "";

        // no data found
        if(data.length === 0){
            suggestions.style.display = "none";
            return;
        }

        suggestions.style.display = "block";

        data.forEach(item => {
    suggestions.innerHTML += `
        <div class="suggestion-item">
            ${item}
        </div>
    `;
});

        // click suggestion
        document.querySelectorAll(".suggestion-item")
        .forEach(element => {

            element.addEventListener("click", function(){

                input.value = this.innerText;

                suggestions.innerHTML = "";

                suggestions.style.display = "none";

            });

        });

    });

});

// hide dropdown when clicking outside
document.addEventListener("click", function(e){

    if(!e.target.closest(".search-box")){
        suggestions.innerHTML = "";
        suggestions.style.display = "none";
    }

});

</script>
    

</body>
</html>
<?php 

include 'src/views/base.php';

?>
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/home.css"/>
    <div class="dashboard home">
        <div class="feed">
            <div class="button-group">
                <button class="your-feed">Your Feed</button>
            </div>
            <div class="following show">
                <?php include 'src/views/feed.php' ?>
            </div>
            <div class="my-logs hidden" data-user-id=<?php echo $_SESSION['user_id']; ?>>
                <div class="log"></div>
                <div class="log"></div>
                <div class="log"></div>
            </div>
        </div>

        <div class="right-feed">
            <div class="feed-data top">


                <div class="percent__container">
                    <div class="percent__item">
                        <h2><span class="current-time"></h2>
                        <h3><span class="day-of-week"></span><span class="month-day"></span>, <span class="month-name"></span></span></h3>
                        <div class="space"><p>Today</p><span class="percent-day-passed"></span></div>
                        <progress class="meter meter--day" min="0" max="100" value="0"></progress>
                    </div>

                    <div class="percent__item">
                        <div class="space">
                            <p>Week (<span class="days-passed-week"></span>/7)</p>
                            <span class="percent-week-passed"></span>
                        </div>
                        <progress class="meter meter--week" min="0" max="100" value="0"></progress>
                    </div>

                    <div class="percent__item">
                        <div class="space">
                            <p>Month (<span class="day-of-month"></span>)</p>
                            <span class="percent-month-passed"></span>
                        </div>
                        <progress class="meter meter--month" min="0" max="100" value="0"></progress>
                    </div>

                    <div class="percent__item">
                        <div class="space">
                            <p>Year (<span class="days-passed-year"></span>/365)</p>
                            <span class="percent-year-passed"></span>
                        </div>
                        <progress class="meter meter--year" min="0" max="100" value="0"></progress>
                    </div>


                </div>
            </div>
            <div class="feed-data bottom">
                <div class="button-group">
                    <button id="about-btn">About</button>
                    <button id="faq-btn">FAQ</button>
                    <button id="contact-btn">Contact</button>
                </div>
                <div>
                    <span>© 2025 LinguaLair S.L.</span>
                </div>
            </div>
        </div>
    </div>
<?php endblock();
?>

<script type="text/javascript" src="public/js/home.js"></script>
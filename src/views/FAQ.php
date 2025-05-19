<?php include 'src/views/base.php'; ?>
<?php startblock('contenido') ?>
<link rel="stylesheet" type="text/css" href="public/css/about-faq.css"/> 
<link rel="stylesheet" type="text/css" href="public/css/achievements.css"/> 
<div class="dashboard">
    <div class="container faq">
        <div class="button-group">
            <h2 class="margin">Frequently Asked Questions</h2>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                What is LinguaLair?
            </div>
            <div class="faq-answer">
                LinguaLair is a productivity application designed specifically for language learners. It helps you track your study time, analyze your progress, and connect with other language learners.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                How can I record my study time?
            </div>
            <div class="faq-answer">
                You can record your study time directly on the platform, specifying the language, activity, and duration.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                What kind of statistics does LinguaLair offer?
            </div>
            <div class="faq-answer">
                LinguaLair provides detailed statistics on your study time, progress per language, and most frequent activities. This data helps you optimize your study plan.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                How can I connect with other language learners?
            </div>
            <div class="faq-answer">
                You can connect with other learners through user profiles, online events, and in-person events organized by LinguaLair.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                Is LinguaLair free to use?
            </div>
            <div class="faq-answer">
                LinguaLair offers both free and paid plans. The free plan gives you access to basic features, while the paid plans offer additional features.
            </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
                Do you need more help?
            </div>
            <div class="faq-answer">
                Check the following documents to help you start this journey with Lingualair:
                <ul>
                    <li><a href="https://github.com/Sonia-E/LinguaLair/blob/main/manuales/Gu%C3%ADa%20R%C3%A1pida.pdf">Quick Guide</a>: Get easily started!</li>
                    <li><a href="https://github.com/Sonia-E/LinguaLair/blob/main/manuales/Manual%20de%20usuario.pdf">User Manual</a>: Learn everything in detail about LinguaLair!</li>
                </ul>
                Is it still not clear enough? You can contact us <a href="contact">here</a>!
            </div>
        </div>
    </div>
    <script>
        const faqItems = document.querySelectorAll('.faq-item');

        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            const answer = item.querySelector('.faq-answer');

            question.addEventListener('click', () => {
                question.classList.toggle('active');
                answer.classList.toggle('active');
            });
        });
    </script>
</div>
<?php endblock() ?>
(function($) {
    $(document).ready(function() {
        const headlines = $('.wp-block-animated-headline-block');

        headlines.each(function() {
            const $headline = $(this);
            const animationStyle = $headline.data('animation-style');
            const animationSpeed = $headline.data('animation-speed');

            // Apply animation speed for CSS-based animations
            if (animationStyle === 'fly-in-left' || animationStyle === 'bounce-hover') {
                $headline.css('animation-duration', (animationSpeed / 1000) + 's');
            }

            // Initialize typewriter animation
            if (animationStyle === 'typewriter') {
                initializeTypewriter($headline);
            }
        });
    });

    function initializeTypewriter($headlineElement) {
        const text = $headlineElement.data('message');
        const speed = parseInt($headlineElement.data('animation-speed'), 10);
        const characters = text.split("");
        let currentText = "";
        $headlineElement.text(""); // Clear initial text
        let i = 0;

        function forward() {
            if (i < characters.length) {
                currentText += characters[i++];
                $headlineElement.text(currentText);
                setTimeout(forward, speed);
            } else {
                setTimeout(backward, 1000);
            }
        }

        function backward() {
            if (currentText.length > 0) {
                currentText = currentText.slice(0, -1);
                $headlineElement.text(currentText);
                setTimeout(backward, 50);
            } else {
                // After deleting, start the final forward animation
                setTimeout(final, 1000);
            }
        }

        function final() {
            currentText = "";
            i = 0;

            function finalForward() {
                if (i < characters.length) {
                    currentText += characters[i++];
                    $headlineElement.text(currentText);
                    setTimeout(finalForward, speed);
                } else {
                    // Animation finished, add final class to remove cursor
                    $headlineElement.addClass("final");
                }
            }
            finalForward();
        }

        // Start the animation sequence
        forward();
    }
})(jQuery);
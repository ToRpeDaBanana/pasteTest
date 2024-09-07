$(document).ready(function() {
    function updateHighlightedCode() {
        const selectedLanguage = $('#paste_form_language').val();
        const content = $('#paste_form_content').val();

        let highlightedCode;

        if (selectedLanguage !== 'none' && selectedLanguage) {
            highlightedCode = `<pre><code class="language-${selectedLanguage}">${Prism.util.encode(content)}</code></pre>`;
        } else {
            // Здесь можно использовать класс по умолчанию для текстов без языка
            highlightedCode = `<pre><code class="language-default">${Prism.util.encode(content)}</code></pre>`;
        }

        $('#highlightedCode').html(highlightedCode);
        Prism.highlightAll(); // Применяем Prism.highlight
    }

    $('#paste_form_content').on('input change', updateHighlightedCode);
    $('#paste_form_language').on('change', function() {
        updateHighlightedCode();
    });

    // Инициализация выделенного текста при загрузке страницы
    updateHighlightedCode();
    
});


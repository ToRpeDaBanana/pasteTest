$(document).ready(function() {
    // Функция для обновления подсвеченного кода на основе выбранного языка и содержимого
    function updateHighlightedCode() {
        const selectedLanguage = $('#paste_form_language').val(); // Получаем выбранный язык из формы
        const content = $('#paste_form_content').val(); // Получаем содержание текста из формы

        let highlightedCode; // Переменная для хранения подсвеченного кода

        // Если выбран язык, применяем к коду соответствующий класс
        if (selectedLanguage !== 'none' && selectedLanguage) {
            highlightedCode = `<pre><code class="language-${selectedLanguage}">${Prism.util.encode(content)}</code></pre>`;
        } else {
            // Используем класс по умолчанию для текстов без языка
            highlightedCode = `<pre><code class="language-default">${Prism.util.encode(content)}</code></pre>`;
        }

        // Обновляем HTML содержимого элемента с подсвеченным кодом
        $('#highlightedCode').html(highlightedCode);
        Prism.highlightAll(); // Применяем Prism.highlight для подсветки синтаксиса
    }

    // Отслеживаем изменения в поле содержания и языке
    $('#paste_form_content').on('input change', updateHighlightedCode);
    $('#paste_form_language').on('change', function() {
        updateHighlightedCode(); // Обновляем подсветку при смене языка
    });

    // Инициализация выделенного текста при загрузке страницы
    updateHighlightedCode();
});

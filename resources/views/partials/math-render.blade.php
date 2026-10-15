{{-- KaTeX math rendering (CDN). Renders Quill formula blots and LaTeX delimiters inside .math-content containers. --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/contrib/auto-render.min.js" crossorigin="anonymous"></script>
<style>
    .math-content .ql-formula { display: inline-block; vertical-align: middle; }
    .math-content .katex { font-size: 1.08em; }
    .math-content sup, .math-content sub { font-size: 0.75em; line-height: 0; }
    .math-content p { margin: 0 0 0.35em; }
</style>
<script>
    window.renderMathContent = function (root) {
        if (typeof katex === 'undefined') {
            setTimeout(() => window.renderMathContent(root), 100);
            return;
        }
        const scope = root || document;
        const containers = scope.classList && scope.classList.contains('math-content')
            ? [scope]
            : scope.querySelectorAll('.math-content');

        containers.forEach((container) => {
            container.querySelectorAll('.ql-formula[data-value]').forEach((el) => {
                const latex = el.getAttribute('data-value');
                if (el.dataset.renderedLatex === latex) {
                    return;
                }
                try {
                    katex.render(latex, el, { throwOnError: false, displayMode: false });
                    el.dataset.renderedLatex = latex;
                } catch (e) {
                    el.textContent = latex;
                }
            });

            if (typeof renderMathInElement !== 'undefined') {
                renderMathInElement(container, {
                    delimiters: [
                        { left: '$$', right: '$$', display: true },
                        { left: '\\[', right: '\\]', display: true },
                        { left: '\\(', right: '\\)', display: false },
                        { left: '$', right: '$', display: false },
                    ],
                    ignoredClasses: ['ql-formula', 'katex'],
                    throwOnError: false,
                });
            }
        });
    };

    (function () {
        let timer = null;
        const schedule = () => {
            clearTimeout(timer);
            timer = setTimeout(() => window.renderMathContent(document), 30);
        };
        document.addEventListener('DOMContentLoaded', schedule);
        document.addEventListener('livewire:navigated', schedule);
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', schedule);
            Livewire.hook('commit', ({ succeed }) => succeed(() => schedule()));
        });
    })();
</script>

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('jquery'));
    } else {
        factory(window.jQuery);
    }
}(function ($) {
    $.extend(true, $.summernote.lang, {
        'en-US': {
            equation: {
                dialogTitle: 'Math Equation Editor',
                tooltip: 'Insert Math Equation',
                insert: 'Insert',
                cancel: 'Cancel',
                latexPlaceholder: 'Type LaTeX markup here...',
                preview: 'Preview:'
            }
        }
    });

    $.extend($.summernote.options, {
        equation: {
            icon: '<b>&sum;</b>'
        }
    });

    var EQUATION_TEMPLATES = [
        { label: '\u00B2', latex: 'x^2', group: 'powers' },
        { label: 'x\u2099', latex: 'x_n', group: 'powers' },
        { label: 'x^{n}', latex: 'x^{n}', group: 'powers' },
        { label: 'x_{n}', latex: 'x_{n}', group: 'powers' },
        { label: 'e^{x}', latex: 'e^{x}', group: 'powers' },
        { label: '\u221A{x}', latex: '\\sqrt{x}', group: 'roots' },
        { label: '\u221B{x}', latex: '\\sqrt[3]{x}', group: 'roots' },
        { label: '\u221C{x}', latex: '\\sqrt[4]{x}', group: 'roots' },
        { label: '\u207F\u221A{x}', latex: '\\sqrt[n]{x}', group: 'roots' },
        { label: 'x/y', latex: '\\frac{x}{y}', group: 'fractions' },
        { label: '\u207F\u2044\u2098', latex: '\\frac{n}{m}', group: 'fractions' },
        { label: 'dy/dx', latex: '\\frac{dy}{dx}', group: 'fractions' },
        { label: '\u2202y/\u2202x', latex: '\\frac{\\partial y}{\\partial x}', group: 'fractions' },
        { label: '\u222B', latex: '\\int', group: 'calculus' },
        { label: '\u222C', latex: '\\iint', group: 'calculus' },
        { label: '\u222D', latex: '\\iiint', group: 'calculus' },
        { label: '\u222E', latex: '\\oint', group: 'calculus' },
        { label: '\u222B_{a}^{b}', latex: '\\int_{a}^{b}', group: 'calculus' },
        { label: '\u2211', latex: '\\sum', group: 'calculus' },
        { label: '\u2211_{i=1}^{n}', latex: '\\sum_{i=1}^{n}', group: 'calculus' },
        { label: '\u220F', latex: '\\prod', group: 'calculus' },
        { label: '\u220F_{i=1}^{n}', latex: '\\prod_{i=1}^{n}', group: 'calculus' },
        { label: '\u2210', latex: '\\coprod', group: 'calculus' },
        { label: '\u2202', latex: '\\partial', group: 'calculus' },
        { label: '\u2207', latex: '\\nabla', group: 'calculus' },
        { label: '\u221E', latex: '\\infty', group: 'calculus' },
        { label: '\u2032', latex: "'", group: 'calculus' },
        { label: '\u2033', latex: "''", group: 'calculus' },
        { label: 'lim', latex: '\\lim', group: 'limits' },
        { label: 'lim_{x\u21920}', latex: '\\lim_{x \\to 0}', group: 'limits' },
        { label: 'lim_{x\u2192\u221E}', latex: '\\lim_{x \\to \\infty}', group: 'limits' },
        { label: '\u03B1', latex: '\\alpha', group: 'greek' },
        { label: '\u03B2', latex: '\\beta', group: 'greek' },
        { label: '\u03B3', latex: '\\gamma', group: 'greek' },
        { label: '\u03B4', latex: '\\delta', group: 'greek' },
        { label: '\u03B5', latex: '\\epsilon', group: 'greek' },
        { label: '\u03B6', latex: '\\zeta', group: 'greek' },
        { label: '\u03B7', latex: '\\eta', group: 'greek' },
        { label: '\u03B8', latex: '\\theta', group: 'greek' },
        { label: '\u03BA', latex: '\\kappa', group: 'greek' },
        { label: '\u03BB', latex: '\\lambda', group: 'greek' },
        { label: '\u03BC', latex: '\\mu', group: 'greek' },
        { label: '\u03BD', latex: '\\nu', group: 'greek' },
        { label: '\u03BE', latex: '\\xi', group: 'greek' },
        { label: '\u03C0', latex: '\\pi', group: 'greek' },
        { label: '\u03C1', latex: '\\rho', group: 'greek' },
        { label: '\u03C3', latex: '\\sigma', group: 'greek' },
        { label: '\u03C4', latex: '\\tau', group: 'greek' },
        { label: '\u03C6', latex: '\\phi', group: 'greek' },
        { label: '\u03C7', latex: '\\chi', group: 'greek' },
        { label: '\u03C8', latex: '\\psi', group: 'greek' },
        { label: '\u03C9', latex: '\\omega', group: 'greek' },
        { label: '\u0393', latex: '\\Gamma', group: 'greek' },
        { label: '\u0394', latex: '\\Delta', group: 'greek' },
        { label: '\u0398', latex: '\\Theta', group: 'greek' },
        { label: '\u039B', latex: '\\Lambda', group: 'greek' },
        { label: '\u039E', latex: '\\Xi', group: 'greek' },
        { label: '\u03A0', latex: '\\Pi', group: 'greek' },
        { label: '\u03A3', latex: '\\Sigma', group: 'greek' },
        { label: '\u03A6', latex: '\\Phi', group: 'greek' },
        { label: '\u03A8', latex: '\\Psi', group: 'greek' },
        { label: '\u03A9', latex: '\\Omega', group: 'greek' },
        { label: '\u2192', latex: '\\rightarrow', group: 'arrows' },
        { label: '\u2190', latex: '\\leftarrow', group: 'arrows' },
        { label: '\u21D2', latex: '\\Rightarrow', group: 'arrows' },
        { label: '\u21D0', latex: '\\Leftarrow', group: 'arrows' },
        { label: '\u2194', latex: '\\leftrightarrow', group: 'arrows' },
        { label: '\u21D4', latex: '\\Leftrightarrow', group: 'arrows' },
        { label: '\u21A6', latex: '\\mapsto', group: 'arrows' },
        { label: '\u27F9', latex: '\\Longrightarrow', group: 'arrows' },
        { label: '\u27F5', latex: '\\Longleftarrow', group: 'arrows' },
        { label: '\u27F7', latex: '\\Longleftrightarrow', group: 'arrows' },
        { label: '\u2192', latex: '\\to', group: 'arrows' },
        { label: '\u2191', latex: '\\uparrow', group: 'arrows' },
        { label: '\u2193', latex: '\\downarrow', group: 'arrows' },
        { label: '()', latex: '\\left(  \\right)', group: 'brackets' },
        { label: '[]', latex: '\\left[  \\right]', group: 'brackets' },
        { label: '{}', latex: '\\left\\{  \\right\\}', group: 'brackets' },
        { label: '\u27E8 \u27E9', latex: '\\left\\langle  \\right\\rangle', group: 'brackets' },
        { label: '||', latex: '\\left|  \\right|', group: 'brackets' },
        { label: '\u2016', latex: '\\left\\|  \\right\\|', group: 'brackets' },
        { label: '\u230A \u230B', latex: '\\left\\lfloor  \\right\\rfloor', group: 'brackets' },
        { label: '\u2308 \u2309', latex: '\\left\\lceil  \\right\\rceil', group: 'brackets' },
        { label: '\u2192', latex: '\\xrightarrow{}', group: 'accents' },
        { label: '\u20D7{v}', latex: '\\vec{v}', group: 'accents' },
        { label: '\u0302{x}', latex: '\\hat{x}', group: 'accents' },
        { label: '\u0304{x}', latex: '\\bar{x}', group: 'accents' },
        { label: '\u0303{x}', latex: '\\tilde{x}', group: 'accents' },
        { label: '\u030C{x}', latex: '\\check{x}', group: 'accents' },
        { label: '\u02D8{x}', latex: '\\dot{x}', group: 'accents' },
        { label: '\u0308{x}', latex: '\\ddot{x}', group: 'accents' },
        { label: '\u00AF{x}', latex: '\\overline{x}', group: 'accents' },
        { label: '\u2192{x}', latex: '\\overrightarrow{AB}', group: 'accents' },
        { label: '\u2260', latex: '\\neq', group: 'relations' },
        { label: '\u2248', latex: '\\approx', group: 'relations' },
        { label: '\u2243', latex: '\\simeq', group: 'relations' },
        { label: '\u2245', latex: '\\cong', group: 'relations' },
        { label: '\u223C', latex: '\\sim', group: 'relations' },
        { label: '\u221D', latex: '\\propto', group: 'relations' },
        { label: '\u2261', latex: '\\equiv', group: 'relations' },
        { label: '\u227A', latex: '\\prec', group: 'relations' },
        { label: '\u227B', latex: '\\succ', group: 'relations' },
        { label: '\u2286', latex: '\\subseteq', group: 'sets' },
        { label: '\u2287', latex: '\\supseteq', group: 'sets' },
        { label: '\u2282', latex: '\\subset', group: 'sets' },
        { label: '\u2283', latex: '\\supset', group: 'sets' },
        { label: '\u2208', latex: '\\in', group: 'sets' },
        { label: '\u2209', latex: '\\notin', group: 'sets' },
        { label: '\u222A', latex: '\\cup', group: 'sets' },
        { label: '\u2229', latex: '\\cap', group: 'sets' },
        { label: '\u2205', latex: '\\emptyset', group: 'sets' },
        { label: '\u2115', latex: '\\mathbb{N}', group: 'sets' },
        { label: '\u2124', latex: '\\mathbb{Z}', group: 'sets' },
        { label: '\u211A', latex: '\\mathbb{Q}', group: 'sets' },
        { label: '\u211D', latex: '\\mathbb{R}', group: 'sets' },
        { label: '\u2102', latex: '\\mathbb{C}', group: 'sets' },
        { label: '\u2133', latex: '\\mathbb{M}', group: 'sets' },
        { label: '\u2295', latex: '\\oplus', group: 'operators' },
        { label: '\u2297', latex: '\\otimes', group: 'operators' },
        { label: '\u2299', latex: '\\odot', group: 'operators' },
        { label: '\u2227', latex: '\\land', group: 'operators' },
        { label: '\u2228', latex: '\\lor', group: 'operators' },
        { label: '\u00AC', latex: '\\lnot', group: 'operators' },
        { label: '\u22A5', latex: '\\bot', group: 'operators' },
        { label: '\u22A4', latex: '\\top', group: 'operators' },
        { label: '\u22A2', latex: '\\vdash', group: 'operators' },
        { label: '\u22A8', latex: '\\models', group: 'operators' },
        { label: '\u22A3', latex: '\\dashv', group: 'operators' },
        { label: '\u22C6', latex: '\\star', group: 'operators' },
        { label: '\u2217', latex: '\\ast', group: 'operators' },
        { label: '\u2218', latex: '\\circ', group: 'operators' },
        { label: '\u2219', latex: '\\bullet', group: 'operators' },
        { label: '\u2234', latex: '\\therefore', group: 'operators' },
        { label: '\u2235', latex: '\\because', group: 'operators' },
        { label: '\u2192', latex: '\\implies', group: 'operators' },
        { label: '\u21D4', latex: '\\iff', group: 'operators' },
        { label: '\\', latex: '\\setminus', group: 'operators' },
        { label: 'matrix', latex: '\\begin{matrix} a & b \\\\ c & d \\end{matrix}', group: 'matrix' },
        { label: 'det', latex: '\\begin{vmatrix} a & b \\\\ c & d \\end{vmatrix}', group: 'matrix' },
        { label: '[]', latex: '\\begin{bmatrix} a & b \\\\ c & d \\end{bmatrix}', group: 'matrix' },
        { label: '()', latex: '\\begin{pmatrix} a & b \\\\ c & d \\end{pmatrix}', group: 'matrix' },
        { label: '||', latex: '\\begin{Vmatrix} a & b \\\\ c & d \\end{Vmatrix}', group: 'matrix' },
        { label: 'cases', latex: '\\begin{cases} x & y \\\\ a & b \\end{cases}', group: 'matrix' },
        { label: '\u2026', latex: '\\dots', group: 'dots' },
        { label: '\u22EF', latex: '\\cdots', group: 'dots' },
        { label: '\u22F1', latex: '\\ddots', group: 'dots' },
        { label: '\u22EE', latex: '\\vdots', group: 'dots' },
        { label: '\u2190', latex: '\\xleftarrow{}', group:'xarrows' },
        { label: '\u2192', latex: '\\xrightarrow{}', group:'xarrows' },
        { label: '\u21D0', latex: '\\xLeftarrow{}', group:'xarrows' },
        { label: '\u21D2', latex: '\\xRightarrow{}', group:'xarrows' },
        { label: '\u2194', latex: '\\xleftrightarrow{}', group:'xarrows' },
        { label: '\u21D4', latex: '\\xLeftrightarrow{}', group:'xarrows' },
        { label: '\u2192', latex: '\\xrightarrow[n]{}', group:'xarrows' },
        { label: '\u2190', latex: '\\xleftarrow[n]{}', group:'xarrows' },
        { label: '\u2225', latex: '\\parallel', group:'geometry' },
        { label: '\u2225', latex: '\\nparallel', group:'geometry' },
        { label: '\u27C2', latex: '\\perp', group:'geometry' },
        { label: '\u25B3', latex: '\\triangle', group:'geometry' },
        { label: '\u2220', latex: '\\angle', group:'geometry' },
        { label: '\u2221', latex: '\\measuredangle', group:'geometry' },
        { label: '\u2222', latex: '\\sphericalangle', group:'geometry' },
        { label: '\u25A1', latex: '\\square', group:'geometry' },
        { label: '\u25CB', latex: '\\circ', group:'geometry' },
        { label: '\u2113', latex: '\\ell', group:'geometry' },
        { label: '\u2118', latex: '\\wp', group:'geometry' },
        { label: '\u25B3', latex: '\\bigtriangleup', group:'geometry' },
        { label: '\u25BD', latex: '\\bigtriangledown', group:'geometry' },
        { label: '\u266D', latex: '\\flat', group:'music' },
        { label: '\u266E', latex: '\\natural', group:'music' },
        { label: '\u266F', latex: '\\sharp', group:'music' },
        { label: '\u2135', latex: '\\aleph', group:'other' },
        { label: '\u210F', latex: '\\hbar', group:'other' },
        { label: '\u0127', latex: '\\hslash', group:'other' },
        { label: '\u212B', latex: '\\AA', group:'other' },
        { label: '\u2111', latex: '\\Im', group:'other' },
        { label: '\u211C', latex: '\\Re', group:'other' },
        { label: 'o', latex: '\\circ', group:'other' },
        { label: '\u2020', latex: '\\dagger', group:'other' },
        { label: '\u2021', latex: '\\ddagger', group:'other' },
        { label: '\u20D7', latex: '\\vec', group:'other' },
        { label: '\u00B0', latex: '^{\\circ}', group:'other' },
    ];

    var GROUP_LABELS = {
        powers: 'Powers',
        roots: 'Roots',
        fractions: 'Fractions',
        calculus: 'Calculus',
        limits: 'Limits',
        greek: 'Greek',
        arrows: 'Arrows',
        brackets: 'Brackets',
        accents: 'Accents',
        relations: 'Relations',
        sets: 'Sets',
        operators: 'Operators',
        matrix: 'Matrix',
        dots: 'Dots',
        xarrows: 'X-Arrows',
        geometry: 'Geometry',
        music: 'Music',
        other: 'Other'
    };

    var GROUP_ORDER = ['powers', 'roots', 'fractions', 'calculus', 'limits', 'greek', 'arrows', 'brackets', 'accents', 'relations', 'sets', 'operators', 'matrix', 'dots', 'xarrows', 'geometry', 'music', 'other'];

    $.extend($.summernote.plugins, {
        'equation': function (context) {
            var self = this;
            var ui = $.summernote.ui;
            var $editor = context.layoutInfo.editor;
            var options = context.options;
            var lang = options.langInfo;
            var groupedTemplates = {};
            EQUATION_TEMPLATES.forEach(function (t) {
                if (!groupedTemplates[t.group]) groupedTemplates[t.group] = [];
                groupedTemplates[t.group].push(t);
            });

            self.events = {};

            context.memo('button.equation', function () {
                var button = ui.button({
                    contents: '<b style="font-size:16px">&sum;</b>',
                    container: false,
                    tooltip: lang.equation.tooltip,
                    click: function () {
                        context.invoke('editor.saveRange');
                        context.invoke('equation.show');
                    }
                });
                return button.render();
            });

            function buildTemplateHTML() {
                var html = '<div class="equation-templates" style="margin-bottom:10px;border-bottom:2px solid #dee2e6;padding-bottom:8px;max-height:300px;overflow-y:auto">';
                GROUP_ORDER.forEach(function (g) {
                    if (!groupedTemplates[g] || groupedTemplates[g].length === 0) return;
                    html += '<div style="margin-bottom:6px">';
                    html += '<div style="font-size:11px;color:#6c757d;margin-bottom:4px;font-weight:600">' + (GROUP_LABELS[g] || g) + '</div>';
                    html += '<div style="display:flex;flex-wrap:wrap;gap:3px">';
                    groupedTemplates[g].forEach(function (t) {
                        html += '<button type="button" class="btn btn-sm btn-outline-secondary eq-tpl-btn" data-latex="' + t.latex.replace(/"/g, '&quot;') + '" title="' + t.latex + '" style="font-size:13px;padding:2px 6px;font-family:serif">' + t.label + '</button>';
                    });
                    html += '</div></div>';
                });
                html += '</div>';
                return html;
            }

            self.initialize = function () {
                var $container = options.dialogsInBody ? $(document.body) : $editor;

                var body = '<div class="form-group">' +
                    buildTemplateHTML() +
                    '<div class="row">' +
                    '<div class="col-sm-12">' +
                    '<div class="row mb-2">' +
                    '<div class="col-sm-12"><label>' + (lang.equation.latexPlaceholder || 'Type LaTeX markup here...') + '</label></div>' +
                    '<div class="col-sm-12"><input class="form-control note-equation-latex" placeholder="\\frac{a}{b}" style="direction:ltr;font-family:monospace"></div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-sm-12"><label>' + (lang.equation.preview || 'Preview:') + '</label></div>' +
                    '<div class="col-sm-12"><div class="note-equation-preview p-2" style="min-height:50px;border:1px solid #dee2e6;border-radius:4px;background:#f8f9fa;text-align:center;font-size:18px"></div></div>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

                self.$dialog = ui.dialog({
                    title: lang.equation.dialogTitle,
                    body: body,
                    footer: '<button class="btn btn-primary note-equation-btn">' + lang.equation.insert + '</button> <button class="btn btn-secondary note-equation-cancel" data-dismiss="modal">' + lang.equation.cancel + '</button>',
                    className: 'equation-dialog'
                }).render().appendTo($container);

                var $latexInput = self.$dialog.find('.note-equation-latex');
                var $preview = self.$dialog.find('.note-equation-preview');
                var $insertBtn = self.$dialog.find('.note-equation-btn');
                var $cancelBtn = self.$dialog.find('.note-equation-cancel');

                self.$dialog.find('.eq-tpl-btn').on('click', function () {
                    var latex = $(this).data('latex') || '';
                    var cursor = $latexInput[0].selectionStart || $latexInput.val().length;
                    var val = $latexInput.val();
                    $latexInput.val(val.slice(0, cursor) + latex + val.slice(cursor));
                    $latexInput.focus();
                    var newPos = cursor + latex.length;
                    $latexInput[0].setSelectionRange(newPos, newPos);
                    doRenderPreview($latexInput, $preview);
                });

                $insertBtn.on('click', function (e) {
                    e.preventDefault();
                    var latex = $latexInput.val().trim();
                    if (!latex) return;
                    var html = $preview.html();
                    if (!html) return;
                    var $mathNode = $('<span class="note-equation" contenteditable="false" style="display:inline-block;padding:0 2px">' + html + '</span>');
                    var $hiddenLatex = $('<span class="note-equation-latex-src" style="display:none">' + latex.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</span>');
                    $mathNode.append($hiddenLatex);
                    context.invoke('editor.restoreRange');
                    context.invoke('editor.focus');
                    context.invoke('editor.insertNode', $mathNode[0]);
                    ui.hideDialog(self.$dialog);
                });

                $cancelBtn.on('click', function () {
                    ui.hideDialog(self.$dialog);
                });

                $latexInput.on('keypress', function (e) {
                    if (e.keyCode === 13 && !e.shiftKey) {
                        e.preventDefault();
                        $insertBtn.trigger('click');
                    }
                });

                function doRenderPreview($input, $previewEl) {
                    var val = $input.val().trim();
                    if (!val) { $previewEl.html(''); return; }
                    try {
                        if (typeof katex !== 'undefined') {
                            katex.render(val, $previewEl[0], { displayMode: true, throwOnError: false });
                        } else {
                            $previewEl.html('<code>' + val.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</code>');
                        }
                    } catch (e) {
                        $previewEl.html('<span style="color:#dc3545;font-size:12px">' + e.message + '</span>');
                        try {
                            if (typeof katex !== 'undefined') {
                                katex.render(val, $previewEl[0], { displayMode: true, throwOnError: false });
                            }
                        } catch (e2) {}
                    }
                }

                $latexInput.on('keyup.eq-preview', function () {
                    doRenderPreview($(this), $preview);
                });

                self._doRenderPreview = doRenderPreview;
            };

            self.show = function () {
                var $latexInput = self.$dialog.find('.note-equation-latex');
                var $preview = self.$dialog.find('.note-equation-preview');

                $latexInput.val('');
                $preview.html('');

                if (self._doRenderPreview) {
                    self._doRenderPreview($latexInput, $preview);
                }

                ui.showDialog(self.$dialog);
                $latexInput.focus();
            };

            self.destroy = function () {
                ui.hideDialog(this.$dialog);
                this.$dialog.remove();
            };
        }
    });
}));

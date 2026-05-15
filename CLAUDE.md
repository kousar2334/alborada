# CSS Guidelines

## No Inline CSS

Never use inline `style` attributes when writing or editing HTML, Blade templates, or any other markup. Always use CSS classes instead.

**Wrong:**
```html
<div style="color: red; margin-top: 16px;">...</div>
```

**Right:**
```html
<div class="text-danger mt-3">...</div>
```

- For utility classes, prefer the existing framework (Bootstrap/Tailwind) already in use in the file.
- For custom styles, add them to the relevant `.css` or `.scss` stylesheet file.
- Never add `style="..."` to any element, even for quick one-off adjustments.

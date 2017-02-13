# FFFields
Craft CMS fields for the front-end.


## Usage

This is all you need to get a field layout in your front end templates:

```twig
<form method="post" accept-charset="UTF-8">
  {{ getCsrfInput() }}
  <input type="hidden" name="action" value="entries/saveEntry">
  <input type="hidden" name="entryId" value="{{ entry.id }}">
  
  {{ craft.fffields.render(entry) }}
  
  <button type="submit" class="ui button">Save</button>
</form>

{% includeJsFile resourceUrl('fffields/dist/main.js') %}
```
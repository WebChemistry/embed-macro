## Installation

```yaml
extensions:
	- WebChemistry\Macros\DI\EmbedMacroExtension
```

## Usage

```html
{define panel}
	<div class="panel">
		<div class="title" n:ifset="$title">{$title}</div>
		<div class="body">
			{$_content|noescape}
		</div>
	</div>
{/define}
```

```html
{embed panel title => 'Bar'}
	Html content with component:
	
	{control foo}
{/embed}

```

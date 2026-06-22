@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ \App\Support\Branding::url('logo-name.png') }}" alt="{{ \App\Support\Branding::name() }}" style="height: 48px; max-height: 48px; width: auto;">
</a>
</td>
</tr>

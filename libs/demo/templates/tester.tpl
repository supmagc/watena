{% extends base.tpl %}
{% if continue %}
	count:
	{% while next() %}
		{[ count ]},
	{%end while %}
	go !!
{% else %}
	no
{% end if %}
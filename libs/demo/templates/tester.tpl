{% extends base.tpl %}
{% if continue %}
	If succeeded !!!
{% end if %}
{% if continue %}
	count:
	{% while next() %}
		{[ count ]},
	{%end while %}
	go !!
{% else %}
	no
{% end if %}
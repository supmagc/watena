{% region begin Extendable %}
Extendableness is proven !!<br />
{% region end Extendable %}
{% region include Extendable %}
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
{% region use MyTestRegion %}
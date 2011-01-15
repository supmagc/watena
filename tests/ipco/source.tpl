{% extends 'base.tpl' %}

{% while read() %}
	within while
	{% cycle bla 0 1 %}
	{% if isPartOneValid() %}
		within if
	{% elseif isPartTwoValid() %}
		within elseif
	{% else %}
		within else
	{% end %}
{% end %}

{% foreach dataArray %}
	within foreach
{% end %}

{% component 'recentArticles' %}
{% template 'recentArticles' %}

{% block 'recentArticles' %}
{% end %}
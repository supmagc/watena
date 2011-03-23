This template is beeing parsed !
{% if getMPublic(sPublic) %}within if
{% elseif ['mPublic', 1, 'mPublic', 0] %}within elseif
{% else %}within else
{% end %}
{% foreach getForeach() %}<p>{[value]}</p>{% end foreach %}
{[TEXT]}
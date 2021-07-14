<!-- BEGIN: Pagination -->
<div class="intro-y col-span-12 flex flex-wrap sm:flex-row sm:flex-nowrap items-center">
    {% if page.items | length > 0 %}
        {% if not(request.getQuery('q') is empty) %}
            {% set q = '&q=' ~ request.getQuery('q') %}
        {% else %}
            {% set q = '' %}
        {% endif %}
        {% set ends_count = 1, middle_count = 1, dots = false %}
        <ul class="pagination">
            <li>
                {{ link_to('users?page=' ~ page.first ~ '&size=' ~ page.limit ~ q,
                    '<i class="w-4 h-4" data-feather="chevrons-left"></i>', 'class': 'pagination__link') }}
            </li>
            <li>
                {{ link_to('users?page=' ~ page.previous ~ '&size=' ~ page.limit ~ q ,
                    '<i class="w-4 h-4" data-feather="chevron-left"></i>', 'class': 'pagination__link') }}
            </li>
            {% for index in 1..page.last %}
                {% if page.current === index %}
                    <li>
                        {{ link_to('users?page=' ~ index ~ '&size=' ~ page.limit ~ q,
                            index, 'class': 'pagination__link pagination__link--active') }}
                    </li>
                    {% set dots = true %}
                {% else %}
                    {% if
                        index <= ends_count or
                        (
                        page.current and
                        index >= page.current - middle_count and
                        index <= page.current + middle_count
                        ) or
                        index > page.last - ends_count %}
                        <li>
                            {{ link_to('users?page=' ~ index ~ '&size=' ~ page.limit ~ q,
                                index, 'class': 'pagination__link') }}
                        </li>
                        {% set dots = true %}
                    {% elseif dots %}
                        <li><span class="pagination__link">...</span></li>
                        {% set dots = false %}
                    {% endif %}
                {% endif %}
            {% endfor %}
            <li>
                {{ link_to('users?page=' ~ page.next ~ '&size=' ~ page.limit ~ q,
                    '<i class="w-4 h-4" data-feather="chevron-right"></i>', 'class': 'pagination__link') }}
            </li>
            <li>
                {{ link_to('users?page=' ~ page.last ~ '&size=' ~ page.limit ~ q,'
                <i class="w-4 h-4" data-feather="chevrons-right"></i>', 'class': 'pagination__link') }}
            </li>
        </ul>
    {% endif %}
    <form id="size-form" method="get" action="" class="w-20 box mt-3 sm:mt-0" style="margin-left: auto">
        <input type="hidden" name="page" value="{{ page.current }}">
        {% if not(request.getQuery('q') is empty) %}
            <input type="hidden" name="q" value="{{ request.getQuery('q') }}">
        {% endif %}
        <select id="size" name="size" class="form-select ">
            {% for value in [1, 2, 3, 4, 5] %}
                <option value="{{ value }}" {% if page.limit == value %}selected{% endif %}>{{ value }}</option>
            {% endfor %}
        </select>
    </form>
</div>
<script type="text/javascript">
    document
        .getElementById('size')
        .addEventListener('change', function () {
            document.getElementById('size-form').submit();
        })
</script>
<!-- END: Pagination -->

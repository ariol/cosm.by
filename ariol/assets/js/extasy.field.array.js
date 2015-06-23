var extasy_field_array = function(field)
{
	var self = this;
	$('#' + field + '_add_button').click(function(){self.add_from_form()});
	this.name = field;
	this.cur_id = 1;
}

extasy_field_array.prototype.add_element = function(elements)
{
	var id = this.cur_id++;
	var row_html = '<tr id="' + this.name + '_element_row_' + id +'">';
	
	var input_html = '';
	
	for(var i in elements)
	{
		row_html += '<td>' + elements[i] + '</td>';
		input_html += '<input type="hidden" name="' + this.name + '[' + id + '][' + i + ']" value="' + elements[i] + '" />';
	}
	row_html += '<td><a href="#" onclick="javascript:' + this.name + '_field_object.remove_element(' + id + ');return false;">[X]</a>';
	row_html += input_html;
	row_html += '</td>';
	row_html += '</tr>';
	
	$('#' + this.name + '_values_tbody').append(row_html);
}

extasy_field_array.prototype.remove_element = function(id)
{
	$('#' + this.name + '_element_row_' + id).remove();
}

extasy_field_array.prototype.add_from_form = function()
{
	var elements = $('.' + this.name + '_add_element');

	var elements_to_add = {};

	var not_empty = false;
	
	for(var i = 0; i < elements.length; i++)
	{
		var element = $(elements[i]);
		elements_to_add[ element.attr('name').replace('_add_element', '') ] = element.val();
		not_empty = not_empty || element.val();
		element.val('');
	}
	if(not_empty)
	{
		this.add_element(elements_to_add);
	}
}
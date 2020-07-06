function generateLink(route, title){
    return "<a ref='"+route+"'>"+title+"</a>";
}
function generateButton(routeEdit, routeDelete, object){
	        var user = object.createdBy.id;
	        var userName = object.createdBy.username;
	return '<a href="'+routeEdit+'" class="btn btn-warning btn-sm edit-reference" user="'+user+'" userName="'+userName+'"><span class="glyphicon glyphicon-edit"></span> Edit</a> <a href="'+routeDelete+'" class="btn btn-danger btn-sm delete-reference" user="'+user+'" userName="'+userName+'"><span class="glyphicon glyphicon-remove-circle"></span> Delete</a>';
}

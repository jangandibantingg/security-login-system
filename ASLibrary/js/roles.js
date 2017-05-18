

var roles = {};

roles.addRole = function () {
	asengine.removeErrorMessages();
	var role = $("#role-name");

	if($.trim(role.val()) == "") {
		asengine.displayErrorMessage(role);
		return;
	}

	$.ajax({
		url: 'ASEngine/ASAjax.php',
		type: 'POST',
		data: {
			action: "addRole",
			role  : role.val()
		},
		success: function (res) {
			try {
				if(res.status == "success") {
					var html  = '<tr class="role-row">';
					html += '<td>'+res.roleName+'</td>';
					html += '<td>0</td>';
					html += '<td><button type="button" class="btn btn-danger" onclick="roles.deleteRole(this,'+res.roleId+');">';
					html += '<i class="icon-trash glyphicon glyphicon-trash"></i> ' + $_lang.delete;
					html += '</button>';
					html += '</td>';
					html += '</tr>';

					$(".roles-table").append(html);
				}
				else
					asengine.displayErrorMessage(role, res.message);
			}
			catch(e) {
				alert($_lang.error_updating_db)
			}
		}
	});
		
};


roles.deleteRole = function (element, roleId) {
	var t = confirm($_lang.are_you_sure);
	if(t) {
		$.ajax({
			url: 'ASEngine/ASAjax.php',
			type: 'POST',
			data: {
				action: "deleteRole",
				roleId: roleId
			},
			success: function () {
				$(element).parents(".role-row").fadeOut("slow", function () {
					$(this).remove();
				});
			}
		});
	}
};
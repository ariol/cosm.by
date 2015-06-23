<input name="<?= $name; ?>" type="hidden" value="<?= $value; ?>" />
<div id="fileupload_<?= $name; ?>">
	<div class="row fileupload-buttonbar">
		<div class="col-lg-7">
			<div class="btn btn-success fileinput-button">
				<i class="glyphicon glyphicon-plus"></i>
				<span>Добавить</span>
				<input type="file" name="files[]" multiple>
			</div>
			<span class="fileupload-process"></span>
		</div>
		<div class="col-lg-5 fileupload-progress fade">
			<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
				<div class="progress-bar progress-bar-success" style="width:0%;"></div>
			</div>
			<div class="progress-extended">&nbsp;</div>
		</div>
	</div>
	<table class="table table-striped" role="presentation">
		<tbody class="files">
		<?php if(strlen($value)) { ?>
			<tr class="template-download fade in">
				<td style="width: 100px;">
					<?php if($file_info['file_type'] == 'image') { ?>
						<span class="preview">
						<a class="fancybox" download="<?= $file_info['name']; ?>" title="<?= $file_info['name']; ?>" href="<?= $value; ?>">
							<img style="max-width: 80px; max-height: 80px;" src="<?= Helpers_File::image2thumb($value); ?>">
						</a>
						</span>
					<? } ?>
				</td>
				<td>
					<p class="name">
						<a target="_blank" title="<?= $file_info['name']; ?>" href="<?= $value; ?>"><?= $file_info['name']; ?></a>
					</p>
				</td>
				<td>
					<span class="size"><?= $file_info['size']; ?> <?= $file_info['type']; ?></span>
				</td>
				<td>
					<button class="btn btn-danger delete" data-url="<?= $value; ?>" data-type="DELETE">
						<i class="glyphicon glyphicon-trash"></i> Удалить
					</button>
				</td>
			</tr>
		<? } ?>
		</tbody>
	</table>
</div>
<script>
	$(function () {
		var oldMain<?= $name; ?> = $('#fileupload_<?= $name;?>').find('.template-download').html();

		$('#fileupload_<?= $name; ?>').fileupload({
			url: '/<?= Kohana::$config->load('extasy.admin_path_prefix') . 'file-uploader'; ?>',
			maxFileSize: 25000000,
			previewCrop: true,
			destroy: function(e, data) {
				if (confirm('Вы уверены?')) {
					$("input[name=<?= $name; ?>]").val('');
					$(data.context).remove();
				}
			},
			process: function(data) {
				$('#fileupload_<?= $name;?>').find('.template-download').remove();
				var tableTrs = $('#fileupload_<?= $name;?>').find('table').find('tr');
				$.each(tableTrs, function(index, tr) {
					if (index + 1 < tableTrs.length) {
						$(tr).remove();
					}
				})
				$('#fileupload_<?= $name; ?>').find('.cancel').on('click', function() {
					$('#fileupload_<?= $name; ?>').find('.template-upload').addClass('template-download');
					$('#fileupload_<?= $name; ?>').find('.template-upload').removeClass('template-upload');
					$('#fileupload_<?= $name; ?>').find('.template-download').html(oldMain<?= $name; ?>);
				});
			},
			success: function(data) {
				$("input[name=<?= $name; ?>]").val(data.files[0].url);
			}
		});
	});
</script>
<script type="text/javascript">
	$(function() {		
		$(".data-table tbody tr.product td:not(:last-child)").click(function() {
			document.location.href = "<?php echo url::site('admin/products/edit'); ?>/" + $(this).parent("tr").attr('data-id');
		});
		
		$(".data-table tr:not(.no-hover) td").hover(function() {
			$(this).parent("tr").find("td").addClass("hover");
		}, function () {
			$(this).parent("tr").find("td").removeClass("hover");
		})
	});
</script>

<table class="data-table">
	<thead>
		<tr>
			<th>Name</th>
			<th width="10%">Price</th>
			<th width="15%">Status</th>
			<th width="20%">Catalog(s)</th>
			<th width="120">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		
		if(isset($products) && is_array($products))
		{
			foreach($products as $product)
			{
			?>
				<tr class="product" data-id="<?php echo $product['product_id']; ?>">
					<td class="first">
						<?php echo $product['product_name']; ?>
					</td>
					<td><?php echo '$'.number_format($product['product_price'],2); ?></td>
					<td><?php 
						if($product['product_enabled']==1) {
							echo 'Enabled';
						} else {
							echo 'Disabled';
						} 
					?></td>
					<td><?php
						if(isset($product['catalogs']) && is_array($product['catalogs']) && count($product['catalogs']) > 0)
						{
							$cats = array();
							foreach($product['catalogs'] as $id=>$cat)
							{
								$cats[] = '<a href="'.url::site('admin/catalogs/list/'.$id).'">'.$cat['catalog_name'].'</a>';
							}
							echo implode(', ', $cats);
						}
						else
						{
							echo '---';
						}
					?></td>
					<td>
						<a href="<?php echo url::site('admin/products/edit/'.$product['product_id']); ?>">Edit</a> &bull;
						<a href="#">Delete</a>
					</td>
				</tr>
			<?php
			}
		}
		?>
	</tbody>
</table>
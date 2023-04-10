<form method="POST" action="<?php echo admin_url('admin.php?page=kma_dashboard'); ?>" style="margin: 0 auto;
padding-top: 20px;">
	<?php wp_nonce_field('auth-action', 'auth-field'); ?>
	<table class="form-table">
		<thead>
		<tr>
			<th><?php echo esc_html__('Settings ', 'KMA-Import-Data'); ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<th>
				<label for="number-product"><?php echo esc_html__('Number product import', 'KMA-Import-Data');
				?></label>
			</th>
			<td>
				<input id="number-product" type="number" min="0" name="kmaImport[numberProduct]"
				       value="<?php echo esc_attr($this->aOptions['numberProduct']?:200); ?>" required
				       class="regular-text"/>
			</td>
		</tr>
		<tr>
			<th><label for="number-review"><?php echo esc_html__('Number review import',
						'KMA-Import-Data'); ?></label>
			</th>
			<td>
				<input id="number-review" type="number" min="0" name="kmaImport[numberReview]"
				       value="<?php echo esc_attr($this->aOptions['numberReview']?:500); ?>" required
                       class="regular-text"/>
			</td>
		</tr>
        <tr>
            <th><label for="number-order"><?php echo esc_html__('Number order import',
						'KMA-Import-Data'); ?></label>
            </th>
            <td>
                <input id="number-order" type="number" min="0" name="kmaImport[numberOrder]"
                       value="<?php echo esc_attr($this->aOptions['numberOrder']?:500); ?>" required
                       class="regular-text"/>
            </td>
        </tr>
		<tr>
			<th><label for="number-comment"><?php echo esc_html__('Number comment import',
						'KMA-Import-Data'); ?></label>
			</th>
			<td>
				<input id="number-comment" type="number" min="0" name="kmaImport[numberComment]"
				       value="<?php echo esc_attr($this->aOptions['numberComment']?:1000); ?>" required
                       class="regular-text"/>
			</td>
		</tr>
		<tr>
			<th><label for="number-customer"><?php echo esc_html__('Number customer import',
						'KMA-Import-Data'); ?></label>
			</th>
			<td>
				<input id="number-customer" type="number" min="0" name="kmaImport[numberCustomer]"
				       value="<?php echo esc_attr($this->aOptions['numberCustomer']?:500); ?>" required
                       class="regular-text"/>
			</td>
		</tr>
		</tbody>
	</table>
	<button id="button-save" class="button button-primary" type="submit"><?php esc_html_e('Save Changes',
			'KMA-Import-Data'); ?></button>
</form>

<table class="table table-bordered ">
	<tbody>
		<tr>
			<th>
				 Name
			</th>
			<td>
				{{$user->first_name}} {{$user->last_name}}
			</td>
		</tr>
		<tr>
			<th>
				 Email
			</th>
			<td>
				{{$user->email}}
			</td>
		</tr>
		<tr>
			<th>
				 Mobile
			</th>
			<td>
				{{$user->mobile}}
			</td>
		</tr>
		
		<tr>
			<th>
				DOB
			</th>
			<td>
				{{date('d-m-Y', strtotime($user->dob))}}
			</td>
		</tr>
		<tr>
			<th>
				About 
			</th>
			<td>
				{{$user->profile->about_info}}
			</td>
		</tr>
		<tr>
			<th>
				Profile Image 
			</th>
			<td>
				<img height="200" width="300" src="{{asset('/public/storage/profile_images/'.$user->profile->profile_image)}}"/>
			</td>
		</tr>
	</tbody>
</table>


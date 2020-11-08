<table class="table table-bordered ">
	<tbody>
		<tr>
			<th>
				User Name
			</th>
			<td>
				{{$feed->user->first_name}} {{$feed->user->last_name}}
			</td>
		</tr>
		<tr>
			<th>
				User Email
			</th>
			<td>
				{{$feed->user->email}}
			</td>
		</tr>
		<tr>
			<th>
				Feed Text
			</th>
			<td>
				{{$feed->feed_text}}
			</td>
		</tr>
		
		<tr>
			<th>
				Like Count
			</th>
			<td>
				{{$feed->likes_count}}
			</td>
		</tr>
		<tr>
			<th>
				Share Count
			</th>
			<td>
				{{$feed->share_count}}
			</td>
		</tr>
		<tr>
			<th>
				Value Count
			</th>
			<td>
				{{$feed->value_added}}
			</td>
		</tr>
		<tr>
			<th>
				Status 
			</th>
			<td>
				{{Helper::$admin_status[$feed->status]}}
			</td>
		</tr>
		{{-- <tr>
			<th>
				Shared By 
			</th>
			<td>
				{{Helper::$admin_status[$feed->status]}}
			</td>
		</tr> --}}
		
	</tbody>
</table>
{{-- <div class="col-md-12 text-center uppercase"><b> Feed Images </b></div>
<table class="table table-bordered">
	<tbody>
	@if($feed->images->count() >0)	
		@foreach($feed->images as $image)
		{{$image}}
			<img src="{{asset('/public/storage/feeds/'.$image->image_path)}}"/>
		@endforeach
	@endif		
	</tbody>
	<tbody>
	@if(count($feed->video)>0)	
		@foreach($feed->images as $images)
			<video>
				
			</video> src="{{asset('/public/storage/feeds/'.$image)}}"/>
		@endforeach
	@endif		
	</tbody>
</table> --}}

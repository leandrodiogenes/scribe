```javascript

//------------------------------------
//------------ ApiRequest ------------
//------------------------------------

@if(count($route['cleanQueryParameters']))
    let params = {!! \Knuckles\Scribe\Tools\WritingUtils::printQueryParamsAsKeyValue($route['cleanQueryParameters'], "\"", ":", 4, "{}") !!};
@endif
@if(count($route['cleanBodyParameters']))
    let params = {!! json_encode($route['cleanBodyParameters'], JSON_PRETTY_PRINT) !!}
@endif

new ApiRequest({
url: "{{ rtrim($baseUrl, '/') }}/{{ ltrim($route['boundUri'], '/') }}",
method: "{{$route['methods'][0]}}",
@if($route['methods'][0] == "PUT")
    raw: true,
@endif
success: function(response){ }
})
@foreach($route['fileParameters'] as $parameter => $file)
    .addParameter('{!! $parameter !!}', document.querySelector('input[name="{!! $parameter !!}"]').files[0]);
@endforeach
@if(count($route['cleanQueryParameters']) or count($route['cleanBodyParameters']))
    .addParameters(params)
@endif
.execute();








//-----------------------------------------
//------------ JAVASCRIPT puro ------------
//-----------------------------------------

const url = new URL("{{ rtrim($baseUrl, '/') }}/{{ ltrim($route['boundUri'], '/') }}");
@if(count($route['cleanQueryParameters']))

    Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));
@endif
@if(!empty($route['headers']))
    let headers = {
    @foreach($route['headers'] as $header => $value)
        "{{$header}}": "{{$value}}",
    @endforeach
    @if(!array_key_exists('Accept', $route['headers']))
        "Accept": "application/json",
    @endif
    };

@endif
@if(count($route['fileParameters']))
    const body = new FormData();
    @foreach($route['cleanBodyParameters'] as $parameter => $value)
        @foreach( \Knuckles\Scribe\Tools\WritingUtils::getParameterNamesAndValuesForFormData($parameter,$value) as $key => $actualValue)
            @php
                try {
                    echo "body.append('$key', '$actualValue')";
                } catch (\Exception $e) {
                    throw new Exception("Há um problema com o parametro {$key} na rota ".$route['methods'][0]." ".$route['uri']);
                }
            @endphp

        @endforeach
    @endforeach
    @foreach($route['fileParameters'] as $parameter => $file)
        body.append('{!! $parameter !!}', document.querySelector('input[name="{!! $parameter !!}"]').files[0]);
    @endforeach
@elseif(count($route['cleanBodyParameters']))
    let body = {!! json_encode($route['cleanBodyParameters'], JSON_PRETTY_PRINT) !!}
@endif

fetch(url, {
method: "{{$route['methods'][0]}}",
@if(count($route['headers']))
    headers: headers,
@endif
@if(count($route['fileParameters']) || count($route['cleanBodyParameters']))
    body: body
@endif
})
.then(response => response.json())
.then(json => console.log(json));


```

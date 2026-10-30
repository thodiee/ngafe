@extends('layouts.app')
@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="glass-dark p-8 rounded-3xl w-full max-w-sm shadow-2xl text-center relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-400 to-blue-500"></div>
        <h2 class="text-2xl font-bold mb-6 text-white"><i class="fa-solid fa-lock text-brand-500 mr-2"></i> Enter POS PIN</h2>
        
        <form action="{{ route('pos.verify') }}" method="POST" id="pinForm">
            @csrf
            <input type="password" name="pin" id="pinDisplay" class="w-full text-center text-3xl tracking-[1em] bg-slate-900 border border-slate-700 rounded-xl py-4 mb-8 text-white focus:outline-none focus:border-brand-500 transition" readonly>
            
            <div class="grid grid-cols-3 gap-4 mb-6">
                @for($i=1; $i<=9; $i++)
                    <button type="button" onclick="addPin('{{$i}}')" class="h-16 rounded-full bg-slate-800 hover:bg-brand-600 transition text-2xl font-semibold text-white shadow-lg active:scale-95">{{ $i }}</button>
                @endfor
                <button type="button" onclick="clearPin()" class="h-16 rounded-full bg-red-900/50 text-red-400 hover:bg-red-600 hover:text-white transition text-xl font-semibold shadow-lg active:scale-95"><i class="fa-solid fa-eraser"></i></button>
                <button type="button" onclick="addPin('0')" class="h-16 rounded-full bg-slate-800 hover:bg-brand-600 transition text-2xl font-semibold text-white shadow-lg active:scale-95">0</button>
                <button type="submit" class="h-16 rounded-full bg-brand-600 hover:bg-brand-500 transition text-xl font-semibold text-white shadow-lg active:scale-95"><i class="fa-solid fa-check"></i></button>
            </div>
        </form>
    </div>
</div>

<script>
    function addPin(num) {
        const display = document.getElementById('pinDisplay');
        if (display.value.length < 6) {
            display.value += num;
        }
    }
    function clearPin() {
        document.getElementById('pinDisplay').value = '';
    }
</script>
@endsection
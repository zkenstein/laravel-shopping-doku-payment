@extends('layouts.profile-master')

@section('profile-content')
		<style>
			.data-row{
				padding-top: 10px;
				padding-bottom: 10px;
			}
		</style>
		<div class="col-md-12">
			<h1>Profile</h1>
			<div class="row" style="padding-top:20px;padding-bottom:20px;">
				<div class="col-md-2 text-center">
					<img src="
					@if(Auth::user()->image == null)
					{{ asset('img/user_profile/default.jpg') }}
					@endif
					" alt="" class="img img-fluid rounded" style="width:80px;">
				</div>
				<div class="col-md-10">
					<div class="row">
						<form action="">
							<div class="col-md-12">
								<input class="btn btn-link" type="submit" name="submit" value="Add/Edit Picture" style="color:#333;">
							</div>
						</form>
						<div class="col-md-12">
							<a href="#" class="btn btn-link">Remove Picture</a>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="data-row col-md-12">
					<div class="row">
						<div class="col-md-2">Nama</div><div class="col-md-10">: {{ $fullname }}</div>
					</div>
				</div>
				<div class="data-row col-md-12">
					<div class="row">
						<div class="col-md-2">Alamat</div><div class="col-md-10">: {{ $address }}</div>
					</div>
				</div>
				<div class="data-row col-md-12">
					<div class="row">
						<div class="col-md-2">Kota</div><div class="col-md-10">: {{ $city }}</div>
					</div>
				</div>
				<div class="data-row col-md-12">
					<div class="row">
						<div class="col-md-2">Provinsi</div><div class="col-md-10">: {{ $state }}</div>
					</div>
				</div>
				<div class="data-row col-md-12">
					<div class="row">
						<div class="col-md-2">Negara</div><div class="col-md-10">: {{ $country }}</div>
					</div>
				</div>
				<div class="data-row col-md-12">
					<div class="row">
						<div class="col-md-2">Kode pos</div><div class="col-md-10">: {{ $zipcode }}</div>
					</div>
				</div>
				<div class="data-row col-md-12">
					<div class="row">
						<div class="col-md-2">Nomor telepon</div><div class="col-md-10">: {{ $phone }}</div>
					</div>
				</div>
				<div class="data-row col-md-12">
					<div class="row">
						<div class="col-md-2">Tanggal lahir</div><div class="col-md-10">: {{ $birthdate }}</div>
					</div>
				</div>

			</div>
			<!-- <p class="text-left">Nama: {{ $fullname }}</p>
			<p class="text-left">Alamat: {{ $address }}</p>
			<p class="text-left">Kota: {{ $city }}</p>
			<p class="text-left">Provinsi: {{ $state }}</p>
			<p class="text-left">Negara: {{ $country }}</p>
			<p class="text-left">Kode pos: {{ $zipcode }}</p>
			<p class="text-left">Nomor telepon: {{ $phone }}</p>
			<p class="text-left">Tanggal lahir: {{ $birthdate }}</p> -->
		</div>
@endsection
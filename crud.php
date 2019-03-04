<?php
	class crud{
		private $dbHost = "localhost";
		private $dbUser = "root";
		private $dbPass = "";
		private $dbName = "sijamu";

		private $hasil = array();
		private $kolom = array();
		private $jmlBaris = "";
		private $pesan = "";

		private $koneksi = "";

		public function koneksi(){  // Fungsi yang digunakan untuk melakukan koneksi dengan server
			$this->koneksi = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass);
			$database = mysqli_select_db($this->koneksi, $this->dbName) or die('Database Tidak Ditemukan');	// Memilih database

		}

		public function tampil($sql){ //Fungsi yang digunakan untuk menampilkan data dengan query dan memasukkan data kedalam array
			$query = mysqli_query($this->koneksi, $sql);
			if($query){
				$this->jmlBaris = mysqli_num_rows($query);	// Menghitung jumlah baris
				for ($i=0; $i<$this->jmlBaris; $i++) {
					$r = mysqli_fetch_array($query);		// Memasukkan data tiap baris kedalam variabel 'r'
					$kunci = array_keys($r);	// Memasukkan nama field kedalam variabel 'kunci'
					for($x=0; $x<count($kunci); $x++){
						if(!is_int($kunci[$x])){
							$this->hasil[$i][$kunci[$x]] = $r[$kunci[$x]];	// Memasukkan data kedalam variabel 'hasil'
						}
					}
				}
				$this->pesan = "Proses Berhasil";
				return true;
			}else{
				$this->pesan = "Proses Gagal";
				return false;
			}
		}
		public function select($tabel, $kolom="*", $kondisi=null, $urut=null, $batas=null, $grup=null){		// Menampilkan data dengan menuliskan nama tabel, kolom dam parameter tambahan
			$q = 'SELECT '.$kolom.' FROM '.$tabel;
			if($kondisi != null){
				$q .= ' WHERE '.$kondisi;	// Menambahkan kondisi kedalam query
			}
			if($grup != null){
				$q .= ' GROUP BY '.$grup;
			}
			if($urut != null){
				$q .= ' ORDER BY '.$urut;	// Menambahakan pengurutan data
			}
			if($batas != null){
				$q .= ' LIMIT '.$batas;		// Menambahkan batas data yang akan ditampilkan
			}
			$query = mysqli_query($this->koneksi, $q);
			if($query){
				$this->jmlBaris = mysqli_num_rows($query);	// Menghitung jumlah baris
				for ($i=0; $i<$this->jmlBaris; $i++) {
					$r = mysqli_fetch_array($query);		// Memasukkan data tiap baris kedalam variabel 'r'
					$kunci = array_keys($r);	// Memasukkan nama field kedalam variabel 'kunci'
					for($x=0; $x<count($kunci); $x++){
						if(!is_int($kunci[$x])){
							$this->hasil[$i][$kunci[$x]] = $r[$kunci[$x]];	// Memasukkan data kedalam variabel 'hasil'
						}
					}
				}
				$this->pesan = "Proses Berhasil";
				return true;
			}else{
				$this->pesan = "Proses Gagal";
				return false;
			}
		}
		public function input($tabel, $nilai){	// Fungsi yang digunakan untuk melakukan input data
			$q = 'INSERT INTO '.$tabel.' VALUES ('.$nilai.')';
			$query = mysqli_query($this->koneksi, $q);
			if($query){
				$this->pesan = "Proses Berhasil";
				return true;
			}else{
				$this->pesan = "Proses Gagal";
				return false;
			}
		}

		public function edit($tabel, $nilai=array(), $id){ // Fungsi ini digunakan untuk melakukan edit data
			$val = array();
			foreach ($nilai as $kunci => $isi) {
				$val[] = $kunci.='="'.$isi.'"';
			}
			$q = 'UPDATE '.$tabel.' SET '.implode(",", $val).' WHERE '.$id;
			if(mysqli_query($this->koneksi, $q)){
				return true;
			}else{
				return false;
			}
		}

		public function hapus($tabel, $id){ // Fungsi ini digunakan untuk meghapus data
			$q = 'DELETE FROM '.$tabel.' WHERE '.$id;
			if(mysqli_query($this->koneksi, $q)){
				return true;
			}else{
				return false;
			}
		}

		public function hasil(){ // Fungsi yang digunakan untuk mengambil data
			$val = $this->hasil;
			$this->hasil = array();
			return $val;
		}

		public function pindahGambar($namaGambar, $tmp){ // Fungsi yang digunakan untuk meng-upload gambar
			$target = 'upload/'.$namaGambar;
			move_uploaded_file($tmp, '../'.$target);
			return $target;
		}

		public function login($username, $pass, $tabel){
			$q = "SELECT * FROM $tabel WHERE username='$username' and password='$pass'";
			$query = mysqli_query($this->koneksi, $q);
			$hasil = mysqli_num_rows($query);
			if($hasil>0){
				return true;
			}else{
				return false;
			}
		}

		public function pindah($halaman){
			header("Location: $halaman");
		}

		public function jmlData(){
			$jml = $this->jmlBaris;
			$this->jmlBaris = "";
			return $jml;
		}

		public function tgl($tanggal){
			$daftarHari = array(
				'Sun' => 'Minggu',
				'Mon' => 'Senin',
				'Tue' => 'Selasa',
				'Wed' => 'Rabu',
				'Thu' => 'Kamis',
				'Fri' => "Jum'at",
				'Sat' => 'Sabtu'
			);
			$daftarBulan = array(
				'Jan' => 'Januari',
				'Feb' => 'Februari',
				'Mar' => 'Maret',
				'Apr' => 'April',
				'May' => 'Mei',
				'Jun' => 'Juni',
				'Jul' => 'Juli',
				'Aug' => 'Agustus',
				'Sep' => 'September',
				'Oct' => 'Oktober',
				'Nov' => 'November',
				'Dec' => 'Desember'
			);
			$hari = date('D', strtotime($tanggal));
			$bulan = date('M', strtotime($tanggal));
			$tgl = date('d', strtotime($tanggal));
			$tahun = date('Y', strtotime($tanggal));
			return $daftarHari["$hari"].", $tgl ".$daftarBulan["$bulan"]." $tahun";
		}

		public function selisihTgl($tgl, $tgl2){
			$a = strtotime($tgl);
			$b = strtotime($tgl2);
			$selisih = abs($b - $a);
			$telat = $selisih/86400;
			$hasil = "";
			if(floor($selisih/(60*60*24))>=365){
				$tahun = floor($selisih/(60*60*24*30*12));
				$hasil .= "$tahun tahun";
				$tgl2 = date("Y-m-d", strtotime("-$tahun year", strtotime($tgl2)));
				$b = strtotime($tgl2);
				$selisih = abs($b - $a);
			}
			if(floor($selisih/(60*60*24*30))>0){
				$bulan = floor($selisih/(60*60*24*30));
				$hasil .= " $bulan bulan";
				$tgl2 = date("Y-m-d", strtotime("-$bulan month", strtotime($tgl2)));
				$b = strtotime($tgl2);
				$selisih = abs($b - $a);
			}
			if(floor($selisih/(60*60*24*7))>0){
				$minggu = floor($selisih/(60*60*24*7));
				$hasil .= " $minggu minggu";
				$tgl2 = date("Y-m-d", strtotime("-$minggu week", strtotime($tgl2)));
				$b = strtotime($tgl2);
				$selisih = abs($b - $a);
			}
			if(floor($selisih/(60*60*24))>0){
				$hari = floor($selisih/(60*60*24));
				$hasil .= " $hari hari";
			}
			return $hasil;
		}

		public function hitungHari($tglAwal, $tglAkhir){
			$a = strtotime($tglAwal);
			$b = strtotime($tglAkhir);
			$selisih = abs($b - $a);
			$telat = $selisih/86400;
			$hari = floor($selisih/(60*60*24));
			$hasil = $hari;
			return $hasil;
		}
	}
?>
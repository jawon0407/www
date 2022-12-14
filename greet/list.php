<? 
	session_start(); 

	@extract($_GET);
	@extract($_POST);
	@extract($_SESSION);

	$table = "greet";			// 게시판 테이블명

	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(E_ALL);
?>
<!DOCTYPE HTML>
<html lang="ko">
<head> 
<meta charset="utf-8">
<link href="../sub4/common/css/sub_style.css" rel="stylesheet" type="text/css" media="all">
<link href="./css/greet.css" rel="stylesheet">
</head>
<?

	include "../lib/dbconn.php";

	if(!$scale){
		$scale=5;			// 한 화면에 표시되는 글 수
	}

    if ($mode=="search")
	{
		if(!$search)
		{
			echo("
				<script>
				 window.alert('검색할 단어를 입력해 주세요!');
			     history.go(-1);
				</script>
			");
			exit;
		}

		$sql = "select * from greet where $find like '%$search%' order by num desc";
	}
	else
	{
		$sql = "select * from greet order by num desc";
	}

	$result = mysql_query($sql, $connect);

	$total_record = mysql_num_rows($result); // 전체 글 수

	// 전체 페이지 수($total_page) 계산 
	if ($total_record % $scale == 0)     
		$total_page = floor($total_record/$scale);      
	else
		$total_page = floor($total_record/$scale) + 1; 
 
	if (!$page)                 // 페이지번호($page)가 0 일 때
		$page = 1;              // 페이지 번호를 1로 초기화
 
	// 표시할 페이지($page)에 따라 $start 계산  
	$start = ($page - 1) * $scale;      

	$number = $total_record - $start;
?>
<body>
	<div id="wrap">
		<? include "../common/sub_header.html" ?>
		<div class="main visual">
			<img src="./../sub4/common/images/yonexzone_main_image.png" alt="요넥스존" />
			<h3>요넥스존</h3>
		</div>
		<div class="subNav">
			<ul class="subNav_wrap">
				<li class="onclick">
					<a href="./list.php">
						<span>요넥스소식</span>
					</a>
				</li>
				<li>
					<a href="./../sub4/sub4_2.html">
						<span>사회공헌</span>
					</a>
				</li>
				<li>
					<a href="./../promotion/list.php">
						<span>사회공헌</span>
					</a>
				</li>
			</ul>
		</div>
		<article id="content">
			<div class="titleArea">
				<h2>요넥스소식</h2>
				<div class="lineMap">
					<span>홈</span>
					&#62;
					<span>요넥스존</span>
					&#62;
					<strong>요넥스소식</strong>
				</div>
			</div>
			<div class="contentArea">
				<div id="col2">    
					<div class="num_container">
						<div id="total_record">
							<span>총 <?= $total_record ?> 개의 게시물이 있습니다.</span>  
						</div>
						<div class="list_counter">
							<label for="scale" class="hidden">리스트개수</label>
							<select id="scale" name="scale" onchange="location.href='list.php?scale='+this.value">
								<option value=''>보기</option>
								<option value='5'>5</option>
								<option value='10'>10</option>
								<option value='10'>15</option>
							</select>
						</div>
					</div>	
					<div id="list_content">
						<div>
							<ul class="list_title">
								<li>번호</li>
								<li>내용</li>
								<li>글쓴이</li>
								<li>작성날짜</li>
								<li>조회수</li>
							</ul>
						</div>
						<?		
							for ($i=$start; $i<$start+$scale && $i < $total_record; $i++){
								mysql_data_seek($result, $i);       
								// 가져올 레코드로 위치(포인터) 이동  
								$row = mysql_fetch_array($result);       
								// 하나의 레코드 가져오기
								
								$item_num     = $row[num];
								$item_id      = $row[id];
								$item_name    = $row[name];
								$item_nick    = $row[nick];
								$item_hit     = $row[hit];

								$item_date    = $row[regist_day];
								$item_date = substr($item_date, 0, 10);  

								$item_subject = str_replace(" ", "&nbsp;", $row[subject]);
						?>
						<a href="view.php?table=<?=$table?>&num=<?=$item_num?>&page=<?=$page?>">
							<div id="list_item">
								<div id="list_item1"><?= $number ?></div>
								<div id="list_item2"><?= $item_subject ?></div>
								<div id="list_item3"><?= $item_nick ?></div>
								<div id="list_item4"><?= $item_date ?></div>
								<div id="list_item5"><?= $item_hit ?></div>
							</div>
						</a>
						<?
							$number--;
						}
						?>
						<div id="page_button">
							<?
								// 게시판 목록 하단에 페이지 링크 번호 출력
								for ($i=1; $i<=$total_page; $i++){
									if ($page == $i){
										echo "<b> $i </b>";
									}
									else{ 
										echo "<a href='list.php?page=$i&scale=$scale'> $i </a>";
									}      
								}
							?>			
						</div>
						<div id="btn_container">
							<a href="list.php?page=<?=$page?>">목록</a>
							<? 
								if($userid){
							?>
							<a href="write_form.php?page=<?=$page?>">글쓰기</a>
							<? } ?>
						</div>
						<div class="search_container">
							<form  name="board_form" method="post" action="list.php?mode=search"> 
								<div id="list_search">
									<div id="find_kind">
										<select name="find">
											<option value='subject'>제목</option>
											<option value='content'>내용</option>
											<option value='nick'>별명</option>
											<option value='name'>이름</option>
										</select>
									</div>
									<div id="list_search4">
										<input type="text" name="search">
										<button type="submit">검색</button>
									</div>
								</div>
							</form>
						</div>	
					</div>
				</div>
			</div>
		</article>
		<? include "../common/sub_footer.html" ?>	
		<a href="#top">
			<div class="go_top">
				<i class="fa-solid fa-arrow-up"></i>
				<span class="hidden">위로 올라가기</span>
			</div>
      	</a>
	</div>
<script src="./../common/js/jquery-1.12.4.min.js"></script>
<script src="./../common/js/jquery-migrate-1.4.1.min.js"></script>
<script src="./../common/js/fullnav.js"></script>
<script src="./../common/js/topBtn.js"></script>
<script src="./../common/js/subskipnav.js"></script>
<script src="https://kit.fontawesome.com/bff332bdcf.js" crossorigin="anonymous"></script>
</body>
</html>

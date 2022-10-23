<?php
require 'validation.php';

session_start();
header('X-FRAME-OPTIONS:DENY');

// エスケープ関数
function h($str)
{
  return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// 入力、確認、完了、 input.php, confirm.php, thanks.php
// CSRF 偽物のinput.php->悪意のあるページ
// input.php
// ページ切り替え
$pageFlag = 0;

// フォームバリデーション
$errors = validation($_POST);

// バリデーションチェックOK→確認画面
if (!empty($_POST['btn_confirm']) && empty($errors)) {
  $pageFlag = 1;
}

// 送信完了
if (!empty($_POST['btn_submit'])) {
  $pageFlag = 2;
}
?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <title>Hello, world!</title>
</head>

<body>

  <?php /* 送信内容の確認画面 */ ?>
  <?php if ($pageFlag === 1) : ?>
    <?php if ($_POST['csrf'] === $_SESSION['csrfToken']) : ?>
      <form method="POST" action="input.php">

        氏名
        <?= h($_POST['your_name']); ?>

        <br>

        メールアドレス
        <?= h($_POST['email']); ?>

        <br>

        ホームページ
        <?= h($_POST['url']); ?>

        <br>

        性別
        <?php
        if ($_POST['gender'] === '0') {
          echo '男性';
        }

        if ($_POST['gender'] === '1') {
          echo '女性';
        }
        ?>

        <br>

        年齢
        <?php
        switch ($_POST['age']) {
          case '1': {
              echo '〜19歳';
              break;
            }
          case '2': {
              echo '20歳〜29歳';
              break;
            }
          case '3': {
              echo '30歳〜39歳';
              break;
            }
          case '4': {
              echo '40歳〜49歳';
              break;
            }
          case '5': {
              echo '50歳〜59歳';
              break;
            }
          case '6': {
              echo '60歳〜';
              break;
            }
          default: {
              echo 'Not case.';
              break;
            }
        }
        ?>

        <br>

        <h1>お問い合わせ内容</h1>
        <?= h($_POST['contact']); ?>
        <br>

        <input type="submit" name="back" value="戻る">
        <input type="submit" name="btn_submit" value="送信する">
        <input type="hidden" name="your_name" value="<?= h($_POST['your_name']); ?>">
        <input type="hidden" name="email" value="<?= h($_POST['email']); ?>">
        <input type="hidden" name="url" value="<?= h($_POST['url']); ?>">
        <input type="hidden" name="gender" value="<?= h($_POST['gender']); ?>">
        <input type="hidden" name="age" value="<?= h($_POST['age']); ?>">
        <input type="hidden" name="contact" value="<?= h($_POST['contact']); ?>">
        <input type="hidden" name="csrf" value="<?= h($_POST['csrf']); ?>">
      </form>
    <?php endif; ?>
  <?php endif; ?>

  <?php /* 送信完了画面 */ ?>
  <?php if ($pageFlag === 2) : ?>
    <?php if ($_POST['csrf'] === $_SESSION['csrfToken']) : ?>

    <?php require '../mainte/insert.php';
      insertContact($_POST);
      ?>

      送信が完了しました。
      <?php unset($_SESSION['csrfToken']) ?>
    <?php endif; ?>
  <?php endif; ?>


  <?php /* 入力画面 */ ?>
  <?php if ($pageFlag === 0) : ?>
    <?php
    if (!isset($_SESSION['csrfToken'])) {
      $csrfToken = bin2hex(random_bytes(32));
      $_SESSION['csrfToken'] = $csrfToken;
    }

    $token = $_SESSION['csrfToken'];
    ?>

    <?php if (!empty($errors) && !empty($_POST['btn_confirm'])) : ?>
      <?= '<ul>'; ?>
      <?php
      foreach ($errors as $error) {
        echo '<li>' . $error . '</li>';
      }
      ?>
      <?= '</ul>'; ?>
    <?php endif; ?>

    <div class="container">
      <div class="row">
        <div class="col-md-6">

          <form method="POST" action="input.php">
            <div class="form-group">
              <label for="your_name">氏名</label>
              <input
                type="text"
                class="form-control"
                id="your_name"
                name="your_name"
                value="<?= empty($_POST['your_name']) ? "" : $_POST['your_name'] ?>"
                required
              >
            </div>

            <div class="form-group">
              <label for="email">メールアドレス</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                value="<?php if (!empty($_POST['email'])) { echo $_POST['email']; } ?>"
                required
              >
            </div>

            <div class="form-group">
              <label for="url">ホームページ</label>
              <input
                type="url"
                class="form-control"
                id="url"
                name="url"
                value="<?php if (!empty($_POST['url'])) { echo $_POST['url']; } ?>"
                required
              >
            </div>

            <div class="form-check form-check-inline" name="gender" id="gender1" value="0">
              <p>性別</p>
              <label class="form-check-label" for="gender1">男性</label>
              <input
                type="radio"
                name="gender"
                value="0"
                <?php if (isset($_POST['gender']) && $_POST['gender'] === '0') { echo 'checked'; } ?>
              >
              <label class="form-check-label" for="gender2">女性</label>
              <input
                type="radio"
                class="form-check-input"
                name="gender"
                id="gender2"
                value="1"
                <?php if (isset($_POST['gender']) && $_POST['gender'] === '1') { echo 'checked'; } ?>
              >
            </div>

            <div class="form-group">
              <label for="age">年齢</label>
              <select class="form-control" id="age" name="age">
                <option value="">選択してください</option>
                <option value="1" selected>〜19歳</option>
                <option value="2">20歳〜29歳</option>
                <option value="3">30歳〜39歳</option>
                <option value="4">40歳〜49歳</option>
                <option value="5">50歳〜59歳</option>
                <option value="6">60歳〜</option>
              </select>
            </div>

            <div class="form-group">
              <label for="contact">お問い合わせ内容</label>
              <textarea class="form-control" id="contact" row="3" name="contact">
                  <?php
                  if (!empty($_POST['contact'])) {
                    echo $_POST['contact'];
                  }
                  ?>
                </textarea>
            </div>

            <div class="form-check"></div>
            <input class="form-check-input" type="checkbox" id="caution" name="caution" value="1">
            <label class="form-check-label" for="caution">注意事項にチェックする</label>
        </div>

        <input class="btn btn-info" type="submit" name="btn_confirm" value="確認する">
        <input type="hidden" name="csrf" value="<?= $token; ?>">
        </form>
      </div>
    </div>
    </div>
  <?php endif; ?>


  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <!-- Option 2: Separate Popper and Bootstrap JS -->
  <!--
     <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
     -->
</body>

</html>
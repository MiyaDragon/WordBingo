<?php

const BINGO = 'yes';
const NOT_BINGO = 'no';

/**
 * ビンゴカードの取得
 *
 * 標準入力から「ビンゴカード内の単語」を取得後、分割して配列に格納。
 * 分割した単語が格納された配列を返す。
 *
 * @param int $cardSize
 * @return array<array<string>>
 */
function getBingoCard(int $cardSize): array
{
    $bingoCard = [];
    for ($i = 0; $i < $cardSize; $i++) {
        $bingoCard[] = explode(" ", trim(fgets(STDIN)));
    }

    return $bingoCard;
}

/**
 * ビンゴカードに穴を開ける
 *
 * 標準入力から「選ばれた単語」を取得後、ビンゴカードの行毎に同じ単語があるか判定。
 * 単語がある場合、該当のキーを取得し、値を空白にする(穴を開ける)。
 * 穴を開けたビンゴカードの配列を返す。
 *
 * @param int $wordSize
 * @param array<array<string>> $bingoCard
 * @return array<array<string>>
 */
function bingoPunch(int $wordSize, array $bingoCard): array
{
    for ($i = 0; $i < $wordSize; $i++) {
        $selectedWord = trim(fgets(STDIN));
        foreach ($bingoCard as $key => $cardLine) {
            if (in_array($selectedWord, $cardLine)) {
                $key2 = array_search($selectedWord, $cardLine);
                $bingoCard[$key][$key2] = "";
            }
        }
    }

    return $bingoCard;
}

/**
 * ビンゴしているか判定する
 *
 * 穴の空いたビンゴカードを行、列、右斜め下がり、左斜め下がりの方向順に、ビンゴしているかを判定。
 * ビンゴしている場合は'yes'、していない場合は'no'を返す。
 *
 * @param array<array<string>> $bingoCard
 * @return string
 */
function bingoJudgement(array $bingoCard): string
{
    foreach ($bingoCard as $cardLine) {
        if (isHorizontalLineBingo($cardLine)) {
            return BINGO;
        }
    }

    if (isVerticalLineBingo($bingoCard)) {
        return BINGO;
    }

    if (isRightSlantingDownLineBingo($bingoCard)) {
        return BINGO;
    }

    if (isLeftSlantingDownLineBingo($bingoCard)) {
        return BINGO;
    }

    return NOT_BINGO;
}

/**
 * ビンゴしているか判定する（行方向）
 *
 * ビンゴカードの行方向の配列から、値の出現回数を値とした配列を取得。
 * 取得した配列の数が１(全て同じ値)なら'true'、それ以外なら'false'を返す。
 *
 * @param array<string> $cardLine
 * @return bool
 */
function isHorizontalLineBingo(array $cardLine): bool
{
    return count(array_count_values($cardLine)) === 1;
}

/**
 * ビンゴしているか判定する（列方向）
 *
 * ビンゴカードの最初の行方向の配列と、2番目の配列との共通項が格納された配列を取得。
 * 取得した配列と3番目以降の行方向の配列との共通項が格納された配列を取得し、
 * 配列の数が0(共通項がない)なら'false'、それ以外なら'true'を返す。
 *
 * @param array<array<string>> $bingoCard
 * @return bool
 */
function isVerticalLineBingo(array $bingoCard): bool
{
    $result = true;
    $firstIntersectAssoc = array_intersect_assoc($bingoCard[0], $bingoCard[1]);
    foreach (array_slice($bingoCard, 2) as $cardLine) {
        if (count(array_intersect_assoc($firstIntersectAssoc, $cardLine)) === 0) {
            $result = false;
            break;
        }
    }

    return $result;
}

/**
 * ビンゴしているか判定する（右斜め下方向）
 *
 * ビンゴカードの配列の先頭から右斜め下方向順に、行方向の配列の値を抜き出し配列に格納。
 * 取得した配列の数が１(全て同じ値)なら'true'、それ以外なら'false'を返す
 *
 * @param array<array<string>> $bingoCard
 * @return bool
 */
function isRightSlantingDownLineBingo(array $bingoCard): bool
{
    $rightSlantingLine = array_map(function ($cardLine, $index) {
        return $cardLine[$index];
    }, $bingoCard, range(0, count($bingoCard) - 1));

    return count(array_count_values($rightSlantingLine)) === 1;
}

/**
 * ビンゴしているか判定する（左斜め下方向）
 *
 * ビンゴカードの配列の先頭から左斜め下方向順に、行方向の配列の値を抜き出し配列に格納。
 * 取得した配列の数が１(全て同じ値)なら'true'、それ以外なら'false'を返す
 *
 * @param array<array<string>> $bingoCard
 * @return bool
 */
function isLeftSlantingDownLineBingo(array $bingoCard): bool
{
    $leftSlantingLine = array_map(function ($cardLine, $index) {
        return $cardLine[$index];
    }, $bingoCard, range(count($bingoCard) - 1, 0));

    return count(array_count_values($leftSlantingLine)) === 1;
}

// カードサイズの取得
$cardSize = (int)trim(fgets(STDIN));
// ビンゴカードの取得
$bingoCard = getBingoCard($cardSize);
// 単語数取得
$wordSize = (int)trim(fgets(STDIN));
// ビンゴカードに穴を開ける
$bingoCard = bingoPunch($wordSize, $bingoCard);
// ビンゴしたかの判定
$bingoResult = bingoJudgement($bingoCard);

echo $bingoResult;

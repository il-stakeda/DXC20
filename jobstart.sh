#!/bin/bash

# 初期カウンタを 1 に設定
i=1

# カウンタが 50 以下の場合にループを実行
while [ $i -le 50 ]; do
    # 'yes > /dev/null' をバックグラウンドで実行
    `yes > /dev/null &`

    # カウンタを 1 増加
    ((i++))
done

# バックグラウンドで実行中のプロセスの確認
echo "50 processes have been started in the background."
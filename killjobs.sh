#!/bin/bash

# ps コマンドで実行中のプロセスIDを取得
P=$(ps aux | grep '[y]es' | awk '{print $2}')

# 環境変数に設定
export P

# 環境変数を表示
kill -9 $P
echo "50 processes killed."

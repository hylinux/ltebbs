#!/bin/bash

for a in `ls`
do
   if [ $a != "convert.sh" ];then
      echo $a
      sed "s/private \$db/public \$db/g" $a && sed "s/private \$db/public \$db/g" $a|tee $a
   fi

done

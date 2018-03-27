#!/bin/bash


# path
P=`dirname "$0"`
echo $P
cd $P
echo `pwd`

# do we have aws installed ?
if ! [ -x "$(command -v aws)" ]; then
	echo 'Error: aws is not installed.' >&2
	exit 1
fi

# do we have ssh key ?
if [ ! -f aws/hellokeys.pem ]; then
    echo "AWS ssh key not found, create"
	aws ec2 delete-key-pair --key-name hellokeys
	aws ec2 create-key-pair --key-name hellokeys --query 'KeyMaterial' --output text > aws/hellokeys.pem
fi

#aws cloudformation create-stack --stack-name hello-world-stack  --template-body file://$PWD/aws/hello-world-aws.json
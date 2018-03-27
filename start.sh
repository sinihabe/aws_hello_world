#!/bin/bash


# path
P=`dirname "$0"`
cd $P

# do we have aws installed ?
if ! [ -x "$(command -v aws)" ]; then
	echo 'Error: aws is not installed ...' >&2
	exit 1
fi

# configure aws
echo "Current aws configuration"
aws configure list

echo "Lets go over with aws config"
aws configure

# set region
aws configure set region us-west-2


# do we have ssh key ?
if [ ! -f aws/hellokeys.pem ]; then
    echo "AWS ssh key not found, creating one ..."
	aws ec2 delete-key-pair --key-name hellokeys
	aws ec2 create-key-pair --key-name hellokeys --query 'KeyMaterial' --output text > aws/hellokeys.pem
	chmod 400 aws/hellokeys.pem
fi

aws cloudformation create-stack --stack-name hello-world-stack  --template-body file://$PWD/aws/hello-world-aws.json

echo "When your build is ready, run following command to get ELB address ..."
echo "aws elbv2 describe-load-balancers  --output=table |grep DNS"

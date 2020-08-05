## Linode Kubernetes Engine Autoscaler

This is a simple autoscaling utility for horizontally scaling Linodes in an LKE
Cluster Pool based on memory or cpu usage.

It's fully dockerised and has a low resource footprint, so you can 
deploy it locally or on the cluster itself.

## Requirements
* Linode Kuberenetes Cluster (LKE) with Metrics Server
* A kubectl config file (usually stored @ ~/.kube/config)

## Usage

You'll need to configure the Docker image with env variables and the kubectl config.

To run locally:
```
docker run -v ~/.kube/config:/root/.kube/config \
-e LINODE_PERSONAL_ACCCESS_TOKEN='xxxx' \
-e LINODE_LKE_CLUSTER_ID='xxxx' \
-e LINODE_LKE_CLUSTER_POOL_ID='xxxx' \
-e LINODE_LKE_CLUSTER_POOL_MINIMUM_NODES='3' \
-e AUTOSCALE_TRIGGER='cpu' \
-e AUTOSCALE_UP_PERCENTAGE='60' \
-e AUTOSCALE_DOWN_PERCENTAGE='30' \
-e AUTOSCALE_QUERY_INTERVAL='10' \
-e AUTOSCALE_THRESHOLD_COUNT='3' \
-e AUTOSCALE_NUMBER_OF_NODES='1' \
-e AUTOSCALE_WAIT_TIME_AFTER_SCALING='180' \
meezaan/linode-k8s-autoscaler
```

For production, you can build a private Docker image and push a kubectl config file 
with a service account's credentials into the image. So, your Dockerfile may look something like:
```
FROM meezaan/linode-k8s-autoscaler

COPY configfile /root/.kube/config
```


## TO DO
* Add some unit tests (it has already been tested against a production cluster)
* More docs
* Production ready K8S manifest

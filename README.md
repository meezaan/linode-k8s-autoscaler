## Linode Kubernetes Engine Autoscaler

This is a simple autoscaling utility for horizontally scaling Linodes in an LKE
Cluster Pool based on memory or cpu usage. Each instance will autoscale based on either memory or cpu. To use both,
you can deploy 2 instances of this utility (usually 1 is enough).

It's fully dockerised (but written in PHP) and has a low resource footprint, so you can 
deploy it locally or on the cluster itself.

## Requirements
* Linode Kuberenetes Cluster (LKE) with Metrics Server
* A kubectl config file (usually stored @ ~/.kube/config)
* A Linode Personal Access Token with access to LKE
* Docker (recommended) or PHP 7.4 (you'll need to setup env vars on your machine / server before using PHP without Docker)

## Published Docker Image
The image for this utility is published @ Docker Hub as meezaan/linode-k8s-autoscaler (https://hub.docker.com/repository/docker/meezaan/linode-k8s-autoscaler).

The latest tag always has the latest code. Also, Docker Hub tags are tied to the tags in this git repository as releases.

## Environment Variables / Configuration
The docker container takes all its configuration via environment variables. Here's a list of what each one does:

| Environment Variable Name | Description  | 
| ------------------------- | ------------ | 
| LINODE_PERSONAL_ACCCESS_TOKEN  | Your Personal Access Token with LKE scope | 
| LINODE_LKE_CLUSTER_ID          | The ID of the LKE Cluster to Autoscale |
| LINODE_LKE_CLUSTER_POOL_ID     | The Node Pool ID within the LKE Cluster to Autoscale |
| LINODE_LKE_CLUSTER_POOL_MINIMUM_NODES | The minimum nodes to keep in the cluster. The cluster won't be scaled down below this.|
| AUTOSCALE_TRIGGER              | 'cpu' or 'memory'
| AUTOSCALE_UP_PERCENTAGE        | At what percentage of 'cpu' or 'memory' to scale up the node pool. Example: 65
| AUTOSCALE_DOWN_PERCENTAGE      | At what percentage of 'cpu' or 'memory' to scale down the node pool. Example: 40
| AUTOSCALE_QUERY_INTERVAL       | How many seconds to wait before each call to the Kubernetes API to check CPU and Memory usage. Example: 10
| AUTOSCALE_THRESHOLD_COUNT      | After how many consecutive matches of AUTOSCALE_UP_PERCENTAGE or AUTOSCALE_DOWN_PERCENTAGE to scale the cluster up or down.
| AUTOSCALE_NUMBER_OF_NODES      | How many nodes to add or remove at one time when scaling the cluster. Example: 1 or 2 or 3
| AUTOSCALE_WAIT_TIME_AFTER_SCALING | How many seconds to wait after scaling up or down to start checking CPU and Memory. This should be set the to give the cluster enough time to adjust itself with the updated number of nodes. Example: 150

To understand the above assuming we have set the following values.
* AUTOSCALE_TRIGGER=memory
* AUTOSCALE_UP_PERCENTAGE=65
* AUTOSCALE_UP_PERCENTAGE=30
* AUTOSCALE_QUERY_INTERVAL=10
* AUTOSCALE_THRESHOLD_COUNT=3
* AUTOSCALE_NUMBER_OF_NODES=1
* AUTOSCALE_WAIT_TIME_AFTER_SCALING=180

With this setup, the autoscaler utility will query the Kuberenetes API every 10 seconds. If with 3 consecutive calls
to the API (effectively meaning over 30 seconds), the memory usage is higher than 65%, 1 more node will be added to the
specified node pool. The utility will wait for 180 seconds and then start querying the API every 10 seconds again.

If with 3 consecutive calls to the API (effectively meaning over 30 seconds), the memory usage is lower than 30%,
1 node will be removed from the specified node pool. The utility will wait for 180 seconds and then start 
querying the API every 10 seconds again.

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
-e AUTOSCALE_WAIT_TIME_AFTER_SCALING='180' meezaan/linode-k8s-autoscaler
```

## Deploying on Kubernetes

For production, you can build a private Docker image and push a kubectl config file 
with a service account's credentials into the image. So, your Dockerfile may look something like:
```
FROM meezaan/linode-k8s-autoscaler

COPY configfile /root/.kube/config
```

Once you've build the image (and let's assume it's called yourspace/k8s-autoscaler:latest), you can deploy 
it with the following manifest:
```
---
apiVersion: apps/v1
kind: Deployment
metadata:
  name: k8s-autoscaler
  namespace: name-of-namespace ####### Change this to the actual namespace
spec:
  replicas: 1
  strategy:
    type: RollingUpdate
    rollingUpdate:
      maxSurge: 1
      maxUnavailable: 0
  selector:
    matchLabels:
      app: k8s-autoscale
  template:
    metadata:
      labels:
        app: k8s-autoscale
    spec:
      imagePullSecrets:
        - name: regcred  ####### Docker registry credentials secret
      containers:
        - name: k8s-autoscale
          image: yourspace/k8s-autoscaler:latest ####### CHANGE THIS TO YOUR ACTUAL DOCKER IMAGE
          env:
            - name:  LINODE_PERSONAL_ACCCESS_TOKEN
              valueFrom:
                secretKeyRef:
                  name: linode-personal-access-token-k8s-autoscaler ####### LINODE PERSONAL ACCESS TOKEN SECRET
                  key: token
            - name:  LINODE_LKE_CLUSTER_ID
              value: ""
            - name:  LINODE_LKE_CLUSTER_POOL_ID
              value: ""
            - name:  AUTOSCALE_TRIGGER
              value: "memory"
            - name:  AUTOSCALE_UP_PERCENTAGE
              value: "60"
            - name:  AUTOSCALE_DOWN_PERCENTAGE
              value: "30"
            - name:  AUTOSCALE_QUERY_INTERVAL
              value: "30"
            - name:  AUTOSCALE_THRESHOLD_COUNT
              value: "3"
            - name:  AUTOSCALE_NUMBER_OF_NODES
              value: "1"
            - name:  AUTOSCALE_WAIT_TIME_AFTER_SCALING
              value: "150"
          resources:
            requests:
              memory: 32Mi
            limits:
              memory: 32Mi

```

The above manifest uses a secret for your 
Linode Personal Access Token and docker registry credentials.

You will need to create these.

### Sizing the Autoscaler Pod
The above pod takes 0.01 CPU and 15MB of memory to run. The memory may 
increase based on the size of the API response, but it returns JSON, so even 
if you have 100+ servers in your cluster you're still only looking and 30MB or so.

### Credits
* https://github.com/travisghansen/kubernetes-client-php
* https://github.com/guzzle/guzzle
* https://github.com/Seldaek/monolog
* The Linode API - https://developers.linode.com/api/v4/lke-clusters
* This utility has been built for https://islamic.network

### Disclaimer
This utility is not affiliated with Linode.
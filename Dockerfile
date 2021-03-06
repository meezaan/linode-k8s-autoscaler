FROM islamicnetwork/php:8.0-cli

COPY . /autoscaler/

RUN cd /autoscaler/ && composer install --no-dev

ENV LINODE_PERSONAL_ACCCESS_TOKEN "XXXXXXXXXXXXXXXXX"
ENV LINODE_LKE_CLUSTER_ID "1234"
ENV LINODE_LKE_CLUSTER_POOL_ID "567890"
ENV LINODE_LKE_CLUSTER_POOL_MINIMUM_NODES "3"

ENV AUTOSCALE_TRIGGER "memory"
# used or requested
ENV AUTOSCALE_TRIGGER_TYPE "requested"
ENV AUTOSCALE_UP_PERCENTAGE "60"
ENV AUTOSCALE_DOWN_PERCENTAGE "40"
ENV AUTOSCALE_RESOURCE_REQUEST_UP_PERCENTAGE "80"
ENV AUTOSCALE_RESOURCE_REQUEST_DOWN_PERCENTAGE "70"
ENV AUTOSCALE_QUERY_INTERVAL "10"
ENV AUTOSCALE_THRESHOLD_COUNT "3"
ENV AUTOSCALE_NUMBER_OF_NODES "1"
ENV AUTOSCALE_WAIT_TIME_AFTER_SCALING "180"

CMD ["php", "/autoscaler/bin/autoscale"]

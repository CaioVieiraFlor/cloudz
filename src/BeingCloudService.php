<?php 

namespace CloudZ;

interface BeingCloudService {
    function upload(CloudServiceFile $file);
    function delete(DeleteCloudServiceFile $file);
}
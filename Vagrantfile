# -*- mode: ruby -*-
# vi: set ft=ruby :

$script = <<-SCRIPT
    echo "Start composer updating. This can take some minutes."
    composer self-update
    cd /var/www
    composer install
    php bin/console doctrine:migrations:migrate
    #php bin/console server:run 192.168.33.91:8000
SCRIPT

Vagrant.configure("2") do |config|

    # This is a version of Scotch Box.
    # Check out https://box.scotch.io to learn more.

    config.vm.box = "scotch/box"
    config.vm.network "private_network", ip: "192.168.33.91"
    config.vm.hostname = "blockchain"
    config.vm.synced_folder ".", "/var/www"

    config.vm.provider "virtualbox" do |v|
      v.memory = 2048
      v.cpus = 2
    end

    # Optional. Make sure to remove other synced_folder line too
    #config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]

    # Optional NFS. Make sure to remove other synced_folder line too
    #config.vm.synced_folder ".", "/var/www", :nfs => { :mount_options => ["dmode=777","fmode=666"] }

    config.vm.provision "shell", path: "setup/install.sh", privileged: true
    config.vm.provision "shell", inline: $script, run: "always", privileged: true
end


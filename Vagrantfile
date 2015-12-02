# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

GI_LOCAL_HTTP = 8080
GI_LOCAL_MYSQL = 3306
GI_LOCAL_MYSQL_PASS = "root"
GI_LOCAL_IP = "192.168.10.11"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "hashicorp/precise32"

  config.vm.network "forwarded_port", guest: 80, host: GI_LOCAL_HTTP
  config.vm.network "forwarded_port", guest: 3306, host: GI_LOCAL_MYSQL

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: GI_LOCAL_IP

  # Enable provisioning with chef solo, specifying a cookbooks path, roles
  # path, and data_bags path (all relative to this Vagrantfile), and adding
  # some recipes and/or roles.
  #

  config.vm.provision :shell, path: "bootstrap.sh"

  # config.berkshelf.berksfile_path = "./cookbooks/gicookbook/Berksfile"
  # config.berkshelf.enabled = true
  #
  # config.vm.provision "chef_zero" do |chef|
  #   chef.cookbooks_path = "./cookbooks"
  #   chef.add_recipe "gicookbook::default"
  #   chef.json = { mysql_password: GI_LOCAL_MYSQL_PASS, local_ip: GI_LOCAL_IP, host_port: GI_LOCAL_HTTP }
  # end
end

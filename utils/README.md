
Fast Firewall Setup, from ChrisTitusTech
(https://github.com/ChrisTitusTech/firewallsetup)
(https://www.youtube.com/watch?v=qPEA6J9pjG8)

This is a script for setting up a firewall with settings for tarpitting ssh and basic protections that everyone needs.

Make the Rules Permanent: Debian-based Distributions
  sudo apt install iptables-persistent
  sudo /etc/init.d/netfilter-persistent save

To run the scripts (firewall, firewall-down, firewall-reload), remember to run as super user
and to change the script itself to be executable (chmod +x firewall*)

NB: by default, these scripts want to be stored at /etc/firewallsetup

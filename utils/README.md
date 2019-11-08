
# Firewall Setup - iptables
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

# Fail2ban Setup
sudo apt-get install fail2ban
The jail.local file contained in this directory is intended to enable additional filters
not included as part of the default (which is sshd only). It should be placed in /etc/fail2ban and the service restarted

NB: Debian distro is 0.9.6. AbuseIPDB was introduced in 0.10.0, so might want to consider manual install?

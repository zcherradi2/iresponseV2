<virtual-mta $P{VMTA}>
    <domain *>
	  route $P{ROOT}
	  use-starttls no
    </domain>
    smtp-source-host $P{IP} $P{DOMAIN} 
</virtual-mta>
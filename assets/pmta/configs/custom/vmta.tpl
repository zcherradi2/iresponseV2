<virtual-mta $p_vmta>
    smtp-source-host $p_ip $p_domain
    $p_dkim
    <domain *>
        max-msg-rate unlimited
        max-smtp-out                            1200
        max-msg-per-connection                  20
        retry-after                             5m
        bounce-after                            24h
        backoff-retry-after                     2m
        backoff-to-normal-after-delivery        true
        backoff-max-msg-rate                    10000/h
        dk-sign                                 yes
        dkim-sign                               yes
        use-starttls                            no
        smtp-pattern-list                       general-errors
        deliver-local-dsn                       no
    </domain>
</virtual-mta>

<domain $p_domain>
    route [127.0.0.1]:25
</domain>
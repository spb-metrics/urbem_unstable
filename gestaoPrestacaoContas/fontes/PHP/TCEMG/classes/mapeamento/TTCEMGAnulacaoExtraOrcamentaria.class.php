<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

class TTCEMGAnulacaoExtraOrcamentaria extends TOrcamentoContaReceita
{

    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
        $stSql = "
                    SELECT tipo_registro
                    , cod_reduzido_aex
                    , cod_orgao
                    , cod_ext
                    , cod_fonte_recurso
                    , categoria
                    , dt_lancamento
                    , dt_anulacao_ext
                    , justificativa_anulacao
                    , vl_anulacao
                    FROM (
                            SELECT
                            10 AS tipo_registro
                            , LPAD(TTE.cod_lote||''||TTE.cod_entidade||TTE.exercicio||(TTE.cod_lote_estorno+1),15,'0') AS cod_reduzido_aex
                            , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                            , LPAD(TT.cod_lote||''||TT.cod_entidade||''||TT.exercicio||''||balancete_extmmaa.cod_plano,15,'0') AS cod_ext
                            , CPR.cod_recurso AS cod_fonte_recurso
                            , CASE WHEN TT.cod_tipo=1 THEN
                                2
                            ELSE
                                1
                            END
                            AS categoria
                            , TO_CHAR(TT.dt_autenticacao,'ddmmyyyy') AS dt_lancamento
                            , TO_CHAR(TTE.dt_autenticacao,'ddmmyyyy') AS dt_anulacao_ext
                            , TTE.observacao AS justificativa_anulacao
                            , CASE WHEN TTE.valor < 0 THEN REPLACE((TTE.valor * -1)::TEXT, '.', ',')
                                   ELSE REPLACE(TTE.valor::TEXT, '.', ',')
                            END as vl_anulacao
                    
                            FROM tesouraria.transferencia AS TT
                            
                            JOIN tesouraria.transferencia_estornada AS TTE
                            ON TTE.cod_lote=TT.cod_lote
                            AND TTE.exercicio=TT.exercicio
                            AND TTE.tipo=TT.tipo
                            AND TTE.cod_entidade=TT.cod_entidade
                    
                            JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = TT.cod_plano_debito
                            AND plano_analitica.exercicio = TT.exercicio
                    
                            JOIN tcemg.balancete_extmmaa
                            ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                            AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                    
                            JOIN contabilidade.plano_conta
                            ON plano_analitica.exercicio = plano_conta.exercicio
                            AND plano_analitica.cod_conta = plano_conta.cod_conta
                    
                            JOIN administracao.configuracao_entidade
                            ON configuracao_entidade.cod_entidade = TT.cod_entidade
                            AND configuracao_entidade.exercicio = TT.exercicio
                            AND configuracao_entidade.cod_modulo = 55
                            AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                    
                            JOIN contabilidade.plano_recurso AS CPR
                            ON CPR.exercicio=plano_analitica.exercicio
                            AND CPR.cod_plano=plano_analitica.cod_plano
                    
                            JOIN contabilidade.lote
                            ON TT.exercicio = lote.exercicio
                            AND TT.cod_entidade = lote.cod_entidade
                            AND TT.tipo = lote.tipo
                            AND TT.cod_lote = lote.cod_lote
                    
                            WHERE balancete_extmmaa.exercicio  = '". $this->getDado('exercicio')."'
                            AND TTE.dt_autenticacao BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                            AND TT.cod_entidade IN (".$this->getDado ( 'entidades' ).")
                            AND TT.cod_tipo IN (1,2)
                    
                            UNION
                    
                            SELECT
                            10 AS tipo_registro
                            , LPAD(TTE.cod_lote||''||TTE.cod_entidade||TTE.exercicio||(TTE.cod_lote_estorno+1),15,'0') AS cod_reduzido_aex
                            , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                            , LPAD(TT.cod_lote||''||TT.cod_entidade||''||TT.exercicio||''||balancete_extmmaa.cod_plano,15,'0') AS cod_ext
                            , CPR.cod_recurso AS cod_fonte_recurso
                            , CASE WHEN TT.cod_tipo=1 THEN
                                2
                            ELSE
                                1
                            END
                            AS categoria
                            , TO_CHAR(TT.dt_autenticacao,'ddmmyyyy') AS dt_lancamento
                            , TO_CHAR(TTE.dt_autenticacao,'ddmmyyyy') AS dt_anulacao_ext
                            , TTE.observacao AS justificativa_anulacao
                            , CASE WHEN TTE.valor < 0 THEN REPLACE((TTE.valor * -1)::TEXT, '.', ',')
                                   ELSE REPLACE(TTE.valor::TEXT, '.', ',')
                            END as vl_anulacao
                    
                            FROM tesouraria.transferencia AS TT
                    
                            JOIN tesouraria.transferencia_estornada AS TTE
                            ON TTE.cod_lote=TT.cod_lote
                            AND TTE.exercicio=TT.exercicio
                            AND TTE.tipo=TT.tipo
                            AND TTE.cod_entidade=TT.cod_entidade
                    
                            JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = TT.cod_plano_credito
                            AND plano_analitica.exercicio = TT.exercicio
                    
                            JOIN tcemg.balancete_extmmaa
                            ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                            AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                    
                            JOIN contabilidade.plano_conta
                            ON plano_analitica.exercicio = plano_conta.exercicio
                            AND plano_analitica.cod_conta = plano_conta.cod_conta
                    
                            JOIN administracao.configuracao_entidade
                            ON configuracao_entidade.cod_entidade = TT.cod_entidade
                            AND configuracao_entidade.exercicio = TT.exercicio
                            AND configuracao_entidade.cod_modulo = 55
                            AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                    
                            JOIN contabilidade.plano_recurso AS CPR
                            ON CPR.exercicio=plano_analitica.exercicio
                            AND CPR.cod_plano=plano_analitica.cod_plano
                    
                            JOIN contabilidade.lote
                            ON TT.exercicio = lote.exercicio
                            AND TT.cod_entidade = lote.cod_entidade
                            AND TT.tipo = lote.tipo
                            AND TT.cod_lote = lote.cod_lote
                    
                            WHERE balancete_extmmaa.exercicio  = '". $this->getDado('exercicio')."'
                            AND TTE.dt_autenticacao BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                            AND TT.cod_entidade IN (".$this->getDado ( 'entidades' ).")
                            AND TT.cod_tipo IN (1,2)
                    
                    ) AS resultado
                    
                    GROUP BY tipo_registro
                    , cod_reduzido_aex
                    , cod_orgao
                    , cod_ext
                    , cod_fonte_recurso
                    , categoria
                    , dt_lancamento
                    , dt_anulacao_ext
                    , justificativa_anulacao
                    , vl_anulacao
                    
                    ORDER BY cod_reduzido_aex
                    
                    
        ";

        return $stSql;
    }

    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
        $stSql = "
                    SELECT tipo_registro
                    , cod_reduzido_aex
                    , nro_op
                    , dt_pagamento
                    , nro_anulacao_op
                    , dt_anulacao_op
                    , vl_anulacao_op
                    
                    FROM (
                            SELECT
                            11 AS tipo_registro
                            , LPAD(TTE.cod_lote||''||TTE.cod_entidade||TTE.exercicio||(TTE.cod_lote_estorno+1),15,'0') AS cod_reduzido_aex
                            , balancete_extmmaa.cod_plano||''||TT.cod_lote||lancamento.sequencia AS nro_op
                            , TO_CHAR(TT.dt_autenticacao, 'ddmmyyyy') AS dt_pagamento
                            , balancete_extmmaa.cod_plano||''||TT.cod_lote||lancamento.sequencia AS nro_anulacao_op
                            , TO_CHAR(TTE.dt_autenticacao,'ddmmyyyy') AS dt_anulacao_op
                            , CASE WHEN valor_lancamento.vl_lancamento < 0 THEN REPLACE((valor_lancamento.vl_lancamento * -1)::TEXT, '.', ',')
                                   ELSE REPLACE(valor_lancamento.vl_lancamento::TEXT, '.', ',')
                            END AS vl_anulacao_op
                            
                            FROM tesouraria.transferencia AS TT
                    
                            JOIN tesouraria.transferencia_estornada AS TTE
                            ON TTE.cod_lote=TT.cod_lote
                            AND TTE.exercicio=TT.exercicio
                            AND TTE.tipo=TT.tipo
                            AND TTE.cod_entidade=TT.cod_entidade
                    
                            JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = TT.cod_plano_debito
                            AND plano_analitica.exercicio = TT.exercicio
                    
                            JOIN tcemg.balancete_extmmaa
                            ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                            AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                    
                            JOIN contabilidade.plano_conta
                            ON plano_analitica.exercicio = plano_conta.exercicio
                            AND plano_analitica.cod_conta = plano_conta.cod_conta
                    
                            JOIN contabilidade.lote
                            ON TT.exercicio = lote.exercicio
                            AND TT.cod_entidade = lote.cod_entidade
                            AND TT.tipo = lote.tipo
                            AND TT.cod_lote = lote.cod_lote
                    
                            JOIN contabilidade.conta_debito
                            ON plano_analitica.exercicio = conta_debito.exercicio
                            AND plano_analitica.cod_plano = conta_debito.cod_plano 
                            AND TT.cod_lote = conta_debito.cod_lote     
                    
                            JOIN contabilidade.valor_lancamento
                            ON conta_debito.exercicio = valor_lancamento.exercicio
                            AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                            AND conta_debito.tipo = valor_lancamento.tipo
                            AND conta_debito.cod_lote = valor_lancamento.cod_lote
                            AND conta_debito.sequencia = valor_lancamento.sequencia
                            AND conta_debito.tipo_valor = valor_lancamento.tipo_valor
                            AND conta_debito.tipo <> 'I'
                    
                            JOIN contabilidade.lancamento
                            ON lancamento.exercicio=valor_lancamento.exercicio
                            AND lancamento.cod_entidade=valor_lancamento.cod_entidade
                            AND lancamento.tipo=valor_lancamento.tipo
                            AND lancamento.cod_lote=valor_lancamento.cod_lote
                            AND lancamento.sequencia=valor_lancamento.sequencia
                    
                            WHERE balancete_extmmaa.exercicio  = '". $this->getDado('exercicio')."'
                            AND TTE.dt_autenticacao BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                            AND TT.cod_entidade IN (".$this->getDado ( 'entidades' ).")
                            AND TT.cod_tipo IN (1,2)
                    
                            UNION
                    
                            SELECT
                            11 AS tipo_registro
                            , LPAD(TTE.cod_lote||''||TTE.cod_entidade||TTE.exercicio||(TTE.cod_lote_estorno+1),15,'0') AS cod_reduzido_aex
                            , balancete_extmmaa.cod_plano||''||TT.cod_lote||lancamento.sequencia AS nro_op
                            , TO_CHAR(TT.dt_autenticacao, 'ddmmyyyy') AS dt_pagamento
                            , balancete_extmmaa.cod_plano||''||TT.cod_lote||lancamento.sequencia AS nro_anulacao_op
                            , TO_CHAR(TTE.dt_autenticacao,'ddmmyyyy') AS dt_anulacao_op
                            , CASE WHEN valor_lancamento.vl_lancamento < 0 THEN REPLACE((valor_lancamento.vl_lancamento * -1)::TEXT, '.', ',')
                                   ELSE REPLACE(valor_lancamento.vl_lancamento::TEXT, '.', ',')
                            END AS vl_anulacao_op
                    
                            FROM tesouraria.transferencia AS TT
                    
                            JOIN tesouraria.transferencia_estornada AS TTE
                            ON TTE.cod_lote=TT.cod_lote
                            AND TTE.exercicio=TT.exercicio
                            AND TTE.tipo=TT.tipo
                            AND TTE.cod_entidade=TT.cod_entidade
                    
                            JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = TT.cod_plano_credito
                            AND plano_analitica.exercicio = TT.exercicio
                    
                            JOIN tcemg.balancete_extmmaa
                            ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                            AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                    
                            JOIN contabilidade.plano_conta
                            ON plano_analitica.exercicio = plano_conta.exercicio
                            AND plano_analitica.cod_conta = plano_conta.cod_conta
                    
                            JOIN contabilidade.lote
                            ON TT.exercicio = lote.exercicio
                            AND TT.cod_entidade = lote.cod_entidade
                            AND TT.tipo = lote.tipo
                            AND TT.cod_lote = lote.cod_lote
                    
                            JOIN contabilidade.conta_credito
                            ON plano_analitica.exercicio = conta_credito.exercicio
                            AND plano_analitica.cod_plano = conta_credito.cod_plano 
                            AND TT.cod_lote = conta_credito.cod_lote
                    
                            JOIN contabilidade.valor_lancamento
                            ON conta_credito.exercicio = valor_lancamento.exercicio
                            AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                            AND conta_credito.tipo = valor_lancamento.tipo
                            AND conta_credito.cod_lote = valor_lancamento.cod_lote
                            AND conta_credito.sequencia = valor_lancamento.sequencia
                            AND conta_credito.tipo_valor = valor_lancamento.tipo_valor
                            AND conta_credito.tipo <> 'I'
                    
                            JOIN contabilidade.lancamento
                            ON lancamento.exercicio=valor_lancamento.exercicio
                            AND lancamento.cod_entidade=valor_lancamento.cod_entidade
                            AND lancamento.tipo=valor_lancamento.tipo
                            AND lancamento.cod_lote=valor_lancamento.cod_lote
                            AND lancamento.sequencia=valor_lancamento.sequencia
                    
                            WHERE balancete_extmmaa.exercicio  = '". $this->getDado('exercicio')."'
                            AND TTE.dt_autenticacao BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                            AND TT.cod_entidade IN (".$this->getDado ( 'entidades' ).")
                            AND TT.cod_tipo IN (1,2)
                    
                    ) AS resultado
                    
                    GROUP BY tipo_registro
                    , cod_reduzido_aex
                    , nro_op
                    , dt_pagamento
                    , nro_anulacao_op
                    , dt_anulacao_op
                    , vl_anulacao_op
                    
                    ORDER BY cod_reduzido_aex
                ";

        return $stSql;
    }
    
    public function __destruct(){}

}
?>

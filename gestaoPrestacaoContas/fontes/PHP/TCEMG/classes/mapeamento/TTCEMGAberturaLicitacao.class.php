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
/**
    * Classe de mapeamento da tabela licitacao.participante
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoParticipante.class.php 57380 2014-02-28 17:45:35Z diogo.zarpelon $

    * Casos de uso: uc-03.05.18
            uc-03.05.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Thiago La Delfa Cabelleira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTCEMGAberturaLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaDetalhamento10(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento10", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }
    
    public function montaRecuperaDetalhamento10()
    {
        $stSql = "
                    SELECT
                        10 AS tipo_registro
                        , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
                        --, LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                        , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                        , licitacao.exercicio AS exercicio_licitacao
                        , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                        , CASE WHEN modalidade.cod_modalidade BETWEEN 1 AND 3 THEN modalidade.cod_modalidade
                             WHEN modalidade.cod_modalidade = 5 THEN 4
                             WHEN modalidade.cod_modalidade = 6 THEN 5
                             WHEN modalidade.cod_modalidade = 7 THEN 6
                             WHEN modalidade.cod_modalidade = 4 THEN 7
                        END AS cod_modalidade_licitacao
                        , modalidade.cod_modalidade AS num_modalidade
                        , CASE WHEN modalidade.cod_modalidade = 11 THEN 2
                             WHEN modalidade.cod_modalidade =  9 THEN 2
                             WHEN modalidade.cod_modalidade = 10 THEN 3
                             ELSE 1
                        END AS natureza_procedimento
                        , TO_CHAR(sw_processo.timestamp,'ddmmyyyy') AS dt_abertura
                        , TO_CHAR(edital.dt_aprovacao_juridico,'ddmmyyyy') AS dt_edital_convite
                        , (SELECT TO_CHAR(publicacao_edital.data_publicacao,'ddmmyyyy')
                                                FROM licitacao.publicacao_edital
                                                JOIN licitacao.veiculos_publicidade
                                                  ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
                                                JOIN licitacao.tipo_veiculos_publicidade
                                                  ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                                                WHERE publicacao_edital.num_edital = edital.num_edital AND publicacao_edital.exercicio = edital.exercicio
                                                  AND tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = 5
                        ) AS dt_publicacao_edital
                        , (SELECT dt_publicacao
                            FROM (SELECT TO_CHAR(publicacao_edital.data_publicacao,'ddmmyyyy') AS dt_publicacao,
                                         row_number() OVER(ORDER BY publicacao_edital.data_publicacao) AS pos
                                                FROM licitacao.publicacao_edital
                                                JOIN licitacao.veiculos_publicidade
                                                  ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
                                                JOIN licitacao.tipo_veiculos_publicidade
                                                  ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                                                WHERE publicacao_edital.num_edital = edital.num_edital AND publicacao_edital.exercicio = edital.exercicio
                                                  AND tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade <> 5
                                ) AS tbl WHERE tbl.pos = 1
                        ) AS dt_publicacao_edital_veiculo1
                        , (SELECT cgm
                            FROM (SELECT veiculos_publicidade.numcgm AS cgm,
                                         row_number() OVER(ORDER BY veiculos_publicidade.numcgm) AS pos
                                                FROM licitacao.publicacao_edital
                                                JOIN licitacao.veiculos_publicidade
                                                  ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
                                                JOIN licitacao.tipo_veiculos_publicidade
                                                  ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                                                WHERE publicacao_edital.num_edital = edital.num_edital AND publicacao_edital.exercicio = edital.exercicio
                                                  AND tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade <> 5
                                ) AS tbl WHERE tbl.pos = 1
                        ) AS veiculo1_publicacao
                        , (SELECT dt_publicacao
                            FROM (SELECT TO_CHAR(publicacao_edital.data_publicacao,'ddmmyyyy') AS dt_publicacao,
                                         row_number() OVER(ORDER BY publicacao_edital.data_publicacao) AS pos
                                                FROM licitacao.publicacao_edital
                                                JOIN licitacao.veiculos_publicidade
                                                  ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
                                                JOIN licitacao.tipo_veiculos_publicidade
                                                  ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                                                WHERE publicacao_edital.num_edital = edital.num_edital AND publicacao_edital.exercicio = edital.exercicio
                                                  AND tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade <> 5
                                ) AS tbl WHERE tbl.pos = 2
                        ) AS dt_publicacao_edital_veiculo2
                        , (SELECT cgm
                            FROM (SELECT veiculos_publicidade.numcgm AS cgm,
                                         row_number() OVER(ORDER BY veiculos_publicidade.numcgm) AS pos
                                                FROM licitacao.publicacao_edital
                                                JOIN licitacao.veiculos_publicidade
                                                  ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
                                                JOIN licitacao.tipo_veiculos_publicidade
                                                  ON tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade = veiculos_publicidade.cod_tipo_veiculos_publicidade
                                                WHERE publicacao_edital.num_edital = edital.num_edital AND publicacao_edital.exercicio = edital.exercicio
                                                  AND tipo_veiculos_publicidade.cod_tipo_veiculos_publicidade <> 5
                                ) AS tbl WHERE tbl.pos = 2
                        ) AS veiculo2_publicacao
                        , TO_CHAR(edital.dt_entrega_propostas,'ddmmyyyy') AS dt_recebimento_doc
                        , criterio_julgamento.cod_criterio AS tipo_licitacao
                        , CASE WHEN tipo_objeto.cod_tipo_objeto = 2 THEN 1
                             WHEN tipo_objeto.cod_tipo_objeto = 1 THEN 2
                             WHEN tipo_objeto.cod_tipo_objeto = 3 THEN 4
                             WHEN tipo_objeto.cod_tipo_objeto = 4 THEN 6
                        END AS natureza_objeto
                        , objeto.descricao AS objeto
                        , CASE WHEN tipo_objeto.cod_tipo_objeto = 2 THEN
                                licitacao.cod_regime
                             ELSE
                                NULL
                        END AS regime_execucao_obras
                        , CASE WHEN (modalidade.cod_modalidade = 1) THEN 
				convidados.nrm_convidado
			  END AS num_convidado
                        , LPAD('',250,'') AS clausula_prorrogacao
                        , 1 AS undade_medida_prazo_execucao
                        , DATE(contrato.fim_execucao)-DATE(contrato.inicio_execucao) AS prazo_execucao
                        , edital.condicoes_pagamento AS forma_pagamento
                        , LPAD('',80,'') AS citerio_aceitabilidade
                        , 2 AS desconto_tabela
                        , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN
                            1
                        ELSE
                            2
                        END AS processo_lote
                        , CASE WHEN fornecedor.tipo = 'M' THEN 1
                             WHEN fornecedor.tipo = 'P' THEN 1
                             WHEN fornecedor.tipo = 'N' THEN 2
                        END AS criterio_desempate
                        , 2 AS destinacao_exclusiva
                        , 2 AS subcontratacao
                        , 2 AS limite_contratacao
    
                    FROM licitacao.licitacao
                    
                    JOIN sw_processo
                      ON sw_processo.cod_processo = licitacao.cod_processo
                     AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                     
                    JOIN licitacao.criterio_julgamento
                      ON criterio_julgamento.cod_criterio = licitacao.cod_criterio
                    
                    JOIN licitacao.edital
                      ON edital.cod_licitacao = licitacao.cod_licitacao
                     AND edital.cod_modalidade = licitacao.cod_modalidade
                     AND edital.cod_entidade = licitacao.cod_entidade
                     AND edital.exercicio_licitacao = licitacao.exercicio
                     AND (
				SELECT edital_anulado.num_edital FROM licitacao.edital_anulado
				WHERE edital_anulado.num_edital=edital.num_edital
				AND edital_anulado.exercicio=edital.exercicio
			 ) IS NULL
                     
                    JOIN compras.objeto
                      ON objeto.cod_objeto = licitacao.cod_objeto
                      
                    JOIN compras.tipo_objeto
                      ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                     
                    JOIN compras.modalidade
                      ON modalidade.cod_modalidade = licitacao.cod_modalidade
                    
                    JOIN compras.mapa
                      ON mapa.exercicio = licitacao.exercicio_mapa
                     AND mapa.cod_mapa = licitacao.cod_mapa
                     
                    JOIN compras.tipo_licitacao
                      ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao
                     
                    JOIN compras.mapa_solicitacao
                      ON mapa_solicitacao.exercicio = mapa.exercicio
                     AND mapa_solicitacao.cod_mapa = mapa.cod_mapa

                    JOIN compras.mapa_cotacao
                      ON mapa_cotacao.exercicio_mapa = mapa.exercicio
                     AND mapa_cotacao.cod_mapa = mapa.cod_mapa
                     
                    JOIN compras.cotacao
                      ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
                     AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                    
                    JOIN compras.julgamento
                      ON julgamento.exercicio = cotacao.exercicio
                     AND julgamento.cod_cotacao = cotacao.cod_cotacao
                     
                    JOIN compras.julgamento_item
                      ON julgamento_item.exercicio = julgamento.exercicio
                     AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                     
                    JOIN compras.cotacao_fornecedor_item
                      ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
                     AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
                     AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
                     AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                     AND cotacao_fornecedor_item.lote = julgamento_item.lote
                     
                    JOIN compras.fornecedor
                      ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor

                    JOIN licitacao.homologacao
                      ON homologacao.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao.cod_entidade=licitacao.cod_entidade
                     AND homologacao.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.cod_item=julgamento_item.cod_item
                     AND (
				SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
				WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
				AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
				AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
				AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
				AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
				AND homologacao.cod_item=homologacao_anulada.cod_item
			 ) IS NULL

                    JOIN compras.solicitacao_homologada
                      ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
                     AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
                     AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao

                    JOIN compras.solicitacao_homologada_reserva
                      ON solicitacao_homologada_reserva.exercicio=solicitacao_homologada.exercicio
                     AND solicitacao_homologada_reserva.cod_entidade=solicitacao_homologada.cod_entidade
                     AND solicitacao_homologada_reserva.cod_solicitacao=solicitacao_homologada.cod_solicitacao

                    JOIN orcamento.despesa
                      ON despesa.exercicio = solicitacao_homologada_reserva.exercicio
                     AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa

                    JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.exercicio = licitacao.exercicio
                     AND configuracao_entidade.cod_entidade = licitacao.cod_entidade
                     
                    JOIN sw_cgm AS responsavel
                      ON responsavel.numcgm = edital.responsavel_juridico
                      
                    JOIN ( SELECT num_documento, numcgm, tipo_documento
                             FROM (
                                    SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                                      FROM sw_cgm_pessoa_fisica
                                      
                                     UNION
                                     
                                    SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                                      FROM sw_cgm_pessoa_juridica
                                ) AS tabela
                            GROUP BY numcgm, num_documento, tipo_documento
                         ) AS documento_pessoa
                      ON documento_pessoa.numcgm = responsavel.numcgm
                    LEFT JOIN licitacao.contrato_licitacao
                           ON contrato_licitacao.cod_licitacao=licitacao.cod_licitacao
			  AND contrato_licitacao.cod_modalidade=licitacao.cod_modalidade
			  AND contrato_licitacao.cod_entidade=licitacao.cod_entidade
			  AND contrato_licitacao.exercicio_licitacao=licitacao.exercicio

                    LEFT JOIN licitacao.contrato
			   ON contrato.num_contrato=contrato_licitacao.num_contrato
			  AND contrato.cod_entidade=contrato_licitacao.cod_entidade
			  AND contrato.exercicio=contrato_licitacao.exercicio
                    
                    LEFT JOIN (SELECT COUNT (*) AS nrm_convidado
                                    , participante.cod_licitacao
                                    , participante.cod_modalidade 
                                    , participante.cod_entidade 
                                    , participante.exercicio 
                                FROM licitacao.participante
                            GROUP BY cod_licitacao,cod_modalidade,cod_entidade,exercicio    
			   ) as convidados
		         ON convidados.cod_licitacao = licitacao.cod_licitacao
			AND convidados.cod_modalidade = licitacao.cod_modalidade
			AND convidados.cod_entidade = licitacao.cod_entidade
			AND convidados.exercicio = licitacao.exercicio
                        
                    WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                      AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
                      AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                      AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                      AND modalidade.cod_modalidade NOT IN (8,9)
           AND NOT EXISTS( SELECT 1
			       FROM licitacao.licitacao_anulada
			      WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
			        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
                    GROUP BY tipo_registro, cod_orgao_resp, cod_unidade_resp, num_processo_licitatorio, licitacao.exercicio,
                             modalidade.cod_modalidade, sw_processo.timestamp, edital.dt_aprovacao_juridico, edital.num_edital, edital.exercicio,
                             criterio_julgamento.cod_criterio, tipo_objeto.cod_tipo_objeto, objeto.descricao, mapa.cod_tipo_licitacao, fornecedor.tipo,
                             contrato.inicio_execucao, contrato.fim_execucao, licitacao.cod_licitacao, licitacao.cod_modalidade, licitacao.cod_entidade, convidados.nrm_convidado

                    ORDER BY licitacao.cod_licitacao, licitacao.cod_modalidade, num_processo_licitatorio, cod_unidade_resp
        ";
        return $stSql;
    }
    
    public function recuperaDetalhamento11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento11()
    {
        $stSql = "
            SELECT
                    11 AS tipo_registro
                  , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
                  --, LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                  , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                  , licitacao.exercicio AS exercicio_licitacao
                  , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                  , homologacao.lote AS num_lote
                  , homologacao.lote AS desc_lote
                  
            FROM licitacao.licitacao
            
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN licitacao.criterio_julgamento
              ON criterio_julgamento.cod_criterio = licitacao.cod_criterio
            
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             AND (
                    SELECT edital_anulado.num_edital FROM licitacao.edital_anulado
                    WHERE edital_anulado.num_edital=edital.num_edital
                    AND edital_anulado.exercicio=edital.exercicio
                 ) IS NULL
             
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
            
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             AND mapa.cod_tipo_licitacao = 2
             
            JOIN compras.tipo_licitacao
              ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa

            JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
            JOIN compras.julgamento
              ON julgamento.exercicio = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao
             
            JOIN compras.julgamento_item
              ON julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             
            JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote = julgamento_item.lote
             
            JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                    SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                    WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                    AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                    AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                    AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                    AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                    AND homologacao.cod_item=homologacao_anulada.cod_item
                    AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL

            JOIN compras.solicitacao_homologada
              ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
             AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
             AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao

            JOIN compras.solicitacao_homologada_reserva
              ON solicitacao_homologada_reserva.exercicio=solicitacao_homologada.exercicio
             AND solicitacao_homologada_reserva.cod_entidade=solicitacao_homologada.cod_entidade
             AND solicitacao_homologada_reserva.cod_solicitacao=solicitacao_homologada.cod_solicitacao

            JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_homologada_reserva.exercicio
             AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa

            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
            JOIN ( SELECT num_documento, numcgm, tipo_documento
                    FROM (
                            SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                            FROM sw_cgm_pessoa_fisica
                            
                            UNION
                            
                            SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                            FROM sw_cgm_pessoa_juridica
                         ) AS tabela
                    GROUP BY numcgm, num_documento, tipo_documento
                 ) AS documento_pessoa
              ON documento_pessoa.numcgm = responsavel.numcgm
            LEFT JOIN licitacao.contrato_licitacao
                   ON contrato_licitacao.cod_licitacao=licitacao.cod_licitacao
                  AND contrato_licitacao.cod_modalidade=licitacao.cod_modalidade
                  AND contrato_licitacao.cod_entidade=licitacao.cod_entidade
                  AND contrato_licitacao.exercicio_licitacao=licitacao.exercicio

            LEFT JOIN licitacao.contrato
                   ON contrato.num_contrato=contrato_licitacao.num_contrato
                  AND contrato.cod_entidade=contrato_licitacao.cod_entidade
                  AND contrato.exercicio=contrato_licitacao.exercicio

            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
            AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
            AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
            AND licitacao.cod_modalidade NOT IN (8,9)
            AND NOT EXISTS( SELECT 1
			       FROM licitacao.licitacao_anulada
			      WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
			        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
             
            GROUP BY licitacao.cod_licitacao, licitacao.cod_modalidade, tipo_registro, cod_orgao_resp, cod_unidade_resp, licitacao.exercicio, num_processo_licitatorio, num_lote

            ORDER BY licitacao.cod_licitacao, licitacao.cod_modalidade, num_processo_licitatorio, cod_unidade_resp
        ";
        
        return $stSql;
    }
    
    public function recuperaDetalhamento12(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento12()
    {
        $stSql = "
            SELECT
                12 AS tipo_registro
                , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
                --, LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                , licitacao.exercicio AS exercicio_licitacao
                , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                , solicitacao_homologada_reserva.cod_item AS cod_item
                --, ordem_item.num_item AS num_item
                , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN
                    1
                ELSE
                    2
                END AS processo_lote
                , homologacao.lote AS num_lote

            FROM licitacao.licitacao
            
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN licitacao.criterio_julgamento
              ON criterio_julgamento.cod_criterio = licitacao.cod_criterio
            
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             AND (
                    SELECT edital_anulado.num_edital FROM licitacao.edital_anulado
                    WHERE edital_anulado.num_edital=edital.num_edital
                    AND edital_anulado.exercicio=edital.exercicio
                 ) IS NULL
             
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
            
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.tipo_licitacao
              ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa

            JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
            JOIN compras.julgamento
              ON julgamento.exercicio = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao
             
            JOIN compras.julgamento_item
              ON julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             
            JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote = julgamento_item.lote
             
            JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                    SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                    WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                    AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                    AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                    AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                    AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                    AND homologacao.cod_item=homologacao_anulada.cod_item
                    AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL

            JOIN compras.solicitacao_homologada
              ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
             AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
             AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao

            JOIN compras.solicitacao_homologada_reserva
              ON solicitacao_homologada_reserva.exercicio=solicitacao_homologada.exercicio
             AND solicitacao_homologada_reserva.cod_entidade=solicitacao_homologada.cod_entidade
             AND solicitacao_homologada_reserva.cod_solicitacao=solicitacao_homologada.cod_solicitacao

            JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_homologada_reserva.exercicio
             AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa

            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
            JOIN ( SELECT num_documento, numcgm, tipo_documento
                    FROM (
                            SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                            FROM sw_cgm_pessoa_fisica
                            
                            UNION
                            
                            SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                            FROM sw_cgm_pessoa_juridica
                         ) AS tabela
                    GROUP BY numcgm, num_documento, tipo_documento
                 ) AS documento_pessoa
              ON documento_pessoa.numcgm = responsavel.numcgm
            LEFT JOIN licitacao.contrato_licitacao
                   ON contrato_licitacao.cod_licitacao=licitacao.cod_licitacao
                  AND contrato_licitacao.cod_modalidade=licitacao.cod_modalidade
                  AND contrato_licitacao.cod_entidade=licitacao.cod_entidade
                  AND contrato_licitacao.exercicio_licitacao=licitacao.exercicio

            LEFT JOIN licitacao.contrato
                   ON contrato.num_contrato=contrato_licitacao.num_contrato
                  AND contrato.cod_entidade=contrato_licitacao.cod_entidade
                  AND contrato.exercicio=contrato_licitacao.exercicio
            
            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
            AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
            AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
            AND licitacao.cod_modalidade NOT IN (8,9)
            AND NOT EXISTS( SELECT 1
			       FROM licitacao.licitacao_anulada
			      WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
			        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
            
            GROUP BY tipo_registro, cod_orgao_resp, cod_unidade_resp, num_processo_licitatorio, licitacao.exercicio,
            modalidade.cod_modalidade, sw_processo.timestamp, edital.dt_aprovacao_juridico, edital.num_edital, edital.exercicio,
            criterio_julgamento.cod_criterio, tipo_objeto.cod_tipo_objeto, objeto.descricao, mapa.cod_tipo_licitacao, fornecedor.tipo,
            contrato.inicio_execucao, contrato.fim_execucao, licitacao.cod_licitacao, licitacao.cod_modalidade, licitacao.cod_entidade,
            solicitacao_homologada_reserva.cod_item, homologacao.lote

            ORDER BY licitacao.cod_licitacao, licitacao.cod_modalidade, num_processo_licitatorio, cod_unidade_resp, solicitacao_homologada_reserva.cod_item
        ";
        return $stSql;
    }
    
    public function recuperaDetalhamento13(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento13",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento13()
    {
        $stSql = "
            SELECT
                13 AS tipo_registro
                , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
                --, LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                , licitacao.exercicio AS exercicio_licitacao
                , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                , solicitacao_homologada_reserva.cod_item AS cod_item
                , homologacao.lote AS num_lote

            FROM licitacao.licitacao
            
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN licitacao.criterio_julgamento
              ON criterio_julgamento.cod_criterio = licitacao.cod_criterio
            
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             AND (
                    SELECT edital_anulado.num_edital FROM licitacao.edital_anulado
                    WHERE edital_anulado.num_edital=edital.num_edital
                    AND edital_anulado.exercicio=edital.exercicio
                 ) IS NULL
             
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
            
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             AND mapa.cod_tipo_licitacao = 2
             
            JOIN compras.tipo_licitacao
              ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa

            JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
            JOIN compras.julgamento
              ON julgamento.exercicio = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao
             
            JOIN compras.julgamento_item
              ON julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             
            JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote = julgamento_item.lote
             
            JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                    SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                    WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                    AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                    AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                    AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                    AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                    AND homologacao.cod_item=homologacao_anulada.cod_item
                    AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL

            JOIN compras.solicitacao_homologada
              ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
             AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
             AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao

            JOIN compras.solicitacao_homologada_reserva
              ON solicitacao_homologada_reserva.exercicio=solicitacao_homologada.exercicio
             AND solicitacao_homologada_reserva.cod_entidade=solicitacao_homologada.cod_entidade
             AND solicitacao_homologada_reserva.cod_solicitacao=solicitacao_homologada.cod_solicitacao

            JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_homologada_reserva.exercicio
             AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa

            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
            JOIN ( SELECT num_documento, numcgm, tipo_documento
                    FROM (
                            SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                            FROM sw_cgm_pessoa_fisica
                            
                            UNION
                            
                            SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                            FROM sw_cgm_pessoa_juridica
                         ) AS tabela
                    GROUP BY numcgm, num_documento, tipo_documento
                 ) AS documento_pessoa
              ON documento_pessoa.numcgm = responsavel.numcgm
            LEFT JOIN licitacao.contrato_licitacao
                   ON contrato_licitacao.cod_licitacao=licitacao.cod_licitacao
                  AND contrato_licitacao.cod_modalidade=licitacao.cod_modalidade
                  AND contrato_licitacao.cod_entidade=licitacao.cod_entidade
                  AND contrato_licitacao.exercicio_licitacao=licitacao.exercicio

            LEFT JOIN licitacao.contrato
                   ON contrato.num_contrato=contrato_licitacao.num_contrato
                  AND contrato.cod_entidade=contrato_licitacao.cod_entidade
                  AND contrato.exercicio=contrato_licitacao.exercicio
            
            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
            AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
            AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
            AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
            AND licitacao.cod_modalidade NOT IN (8,9)
            AND NOT EXISTS( SELECT 1
			       FROM licitacao.licitacao_anulada
			      WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
			        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
            
            GROUP BY tipo_registro, cod_orgao_resp, cod_unidade_resp, num_processo_licitatorio, licitacao.exercicio,
            modalidade.cod_modalidade, sw_processo.timestamp, edital.dt_aprovacao_juridico, edital.num_edital, edital.exercicio,
            criterio_julgamento.cod_criterio, tipo_objeto.cod_tipo_objeto, objeto.descricao, mapa.cod_tipo_licitacao, fornecedor.tipo,
            contrato.inicio_execucao, contrato.fim_execucao, licitacao.cod_licitacao, licitacao.cod_modalidade, licitacao.cod_entidade,
            solicitacao_homologada_reserva.cod_item, homologacao.lote

            ORDER BY licitacao.cod_licitacao, licitacao.cod_modalidade, num_processo_licitatorio, cod_unidade_resp, solicitacao_homologada_reserva.cod_item
        ";
        
        return $stSql;
    }
    
    public function recuperaDetalhamento14(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento14",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento14()
    {
        $stSql = "
            SELECT
                14 AS tipo_registro
                , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
                --, LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                , licitacao.exercicio AS exercicio_licitacao
                , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN
			homologacao.lote
		  ELSE
			NULL
		  END AS num_lote
                , solicitacao_homologada_reserva.cod_item AS cod_item
                , TO_CHAR(cotacao.timestamp,'ddmmyyyy') AS dt_cotacao
                , CASE WHEN tipo_objeto.cod_tipo_objeto = 4 THEN ('0,0000')
			ELSE ((cotacao_fornecedor_item.vl_cotacao/mapa_item.quantidade)::numeric(14,4))::VARCHAR
			END AS vl_cot_precos_unitario
                , mapa_item.quantidade AS quantidade
                , '0,00' AS vl_min_alien_bens

            FROM licitacao.licitacao
            
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN licitacao.criterio_julgamento
              ON criterio_julgamento.cod_criterio = licitacao.cod_criterio
            
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             AND (
                    SELECT edital_anulado.num_edital FROM licitacao.edital_anulado
                    WHERE edital_anulado.num_edital=edital.num_edital
                    AND edital_anulado.exercicio=edital.exercicio
                 ) IS NULL
             
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
            
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.tipo_licitacao
              ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa

            JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa  
           
            JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
            JOIN compras.julgamento
              ON julgamento.exercicio = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao
             
            JOIN compras.julgamento_item
              ON julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             
            JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote = julgamento_item.lote
             
            JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                    SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                    WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                    AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                    AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                    AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                    AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                    AND homologacao.cod_item=homologacao_anulada.cod_item
                    AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL

            JOIN compras.mapa_item
              ON mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             AND mapa_item.cod_item = homologacao.cod_item

            JOIN compras.solicitacao_homologada
              ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
             AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
             AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao

            JOIN compras.solicitacao_homologada_reserva
              ON solicitacao_homologada_reserva.exercicio=solicitacao_homologada.exercicio
             AND solicitacao_homologada_reserva.cod_entidade=solicitacao_homologada.cod_entidade
             AND solicitacao_homologada_reserva.cod_solicitacao=solicitacao_homologada.cod_solicitacao
             AND solicitacao_homologada_reserva.cod_item=homologacao.cod_item

            JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_homologada_reserva.exercicio
             AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa

            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
            JOIN ( SELECT num_documento, numcgm, tipo_documento
                    FROM (
                            SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                            FROM sw_cgm_pessoa_fisica
                            
                            UNION
                            
                            SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                            FROM sw_cgm_pessoa_juridica
                         ) AS tabela
                    GROUP BY numcgm, num_documento, tipo_documento
                 ) AS documento_pessoa
              ON documento_pessoa.numcgm = responsavel.numcgm
            LEFT JOIN licitacao.contrato_licitacao
                   ON contrato_licitacao.cod_licitacao=licitacao.cod_licitacao
                  AND contrato_licitacao.cod_modalidade=licitacao.cod_modalidade
                  AND contrato_licitacao.cod_entidade=licitacao.cod_entidade
                  AND contrato_licitacao.exercicio_licitacao=licitacao.exercicio

            LEFT JOIN licitacao.contrato
                   ON contrato.num_contrato=contrato_licitacao.num_contrato
                  AND contrato.cod_entidade=contrato_licitacao.cod_entidade
                  AND contrato.exercicio=contrato_licitacao.exercicio
              
            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
              AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
              AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND licitacao.cod_modalidade NOT IN (8,9)
              AND NOT EXISTS( SELECT 1
			       FROM licitacao.licitacao_anulada
			      WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
			        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
              
            GROUP BY tipo_registro, cod_orgao_resp, cod_unidade_resp, num_processo_licitatorio, licitacao.exercicio,
            modalidade.cod_modalidade, sw_processo.timestamp, edital.dt_aprovacao_juridico, edital.num_edital, edital.exercicio,
            criterio_julgamento.cod_criterio, tipo_objeto.cod_tipo_objeto, objeto.descricao, mapa.cod_tipo_licitacao, fornecedor.tipo,
            contrato.inicio_execucao, contrato.fim_execucao, licitacao.cod_licitacao, licitacao.cod_modalidade, licitacao.cod_entidade,
            solicitacao_homologada_reserva.cod_item, homologacao.lote, cotacao.timestamp, cotacao_fornecedor_item.vl_cotacao, mapa_item.quantidade

            ORDER BY licitacao.cod_licitacao, licitacao.cod_modalidade, num_processo_licitatorio, cod_unidade_resp, solicitacao_homologada_reserva.cod_item
        ";
        return $stSql;
    }
    
    public function recuperaDetalhamento15(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento15",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento15()
    {
        $stSql = "
            SELECT
                    15 AS tipo_registro
                  , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
                  --, LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                  , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                  , licitacao.exercicio AS exercicio_licitacao
                  , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                  , mapa_item.lote AS num_lote
                  , mapa_item.cod_item AS cod_item
                  , (mapa_item.vl_total / mapa_item.quantidade) AS vl_item
                  
            FROM licitacao.licitacao
            
            JOIN licitacao.participante
              ON participante.cod_licitacao = licitacao.cod_licitacao
             AND participante.cod_modalidade = licitacao.cod_modalidade
             AND participante.cod_entidade = licitacao.cod_entidade
             AND participante.exercicio = licitacao.exercicio
             
            JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
              
            JOIN compras.nota_fiscal_fornecedor
              ON nota_fiscal_fornecedor.cgm_fornecedor = fornecedor.cgm_fornecedor
             
            JOIN compras.nota_fiscal_fornecedor_ordem
              ON nota_fiscal_fornecedor_ordem.cgm_fornecedor = nota_fiscal_fornecedor.cgm_fornecedor
             AND nota_fiscal_fornecedor_ordem.cod_nota = nota_fiscal_fornecedor.cod_nota
             
            JOIN compras.ordem
              ON ordem.exercicio = nota_fiscal_fornecedor_ordem.exercicio
             AND ordem.cod_entidade = nota_fiscal_fornecedor_ordem.cod_entidade
             AND ordem.cod_ordem = nota_fiscal_fornecedor_ordem.cod_ordem
             AND ordem.tipo = nota_fiscal_fornecedor_ordem.tipo
             
            JOIN compras.ordem_item
              ON ordem_item.cod_entidade = ordem.cod_entidade
             AND ordem_item.cod_ordem = ordem.cod_ordem
             AND ordem_item.exercicio = ordem.exercicio
             AND ordem_item.tipo = ordem.tipo
            
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
            JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_item_dotacao.exercicio
             AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa
             
            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = despesa.exercicio
             AND configuracao_entidade.cod_entidade = despesa.cod_entidade
              
            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
              AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
              AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade NOT IN (8,9) 
              AND NOT EXISTS( SELECT 1
			       FROM licitacao.licitacao_anulada
			      WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
			        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
              
            GROUP BY tipo_registro, cod_orgao_resp, cod_unidade_resp, exercicio_licitacao, num_lote, cod_item
        ";
        
        return $stSql;
    }
    
    public function recuperaDetalhamento16(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento16",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento16()
    {
        $stSql = "
            SELECT
                16 AS tipo_registro
                , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao_resp
                --, LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade_resp
                , licitacao.exercicio AS exercicio_licitacao
                , licitacao.exercicio::VARCHAR || LPAD(licitacao.cod_entidade::VARCHAR,2,'0') || LPAD(licitacao.cod_modalidade::VARCHAR,2,'0') || LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                , LPAD(orgao_despesa.valor::varchar,2,'0') AS cod_orgao
                , LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade
                , despesa.cod_funcao AS cod_funcao
                , despesa.cod_subfuncao AS cod_subfuncao
                , despesa.cod_programa AS cod_programa
                , despesa_acao.cod_acao AS id_acao
                , '' AS id_subacao
                , (LPAD(''||REPLACE(conta_despesa.cod_estrutural, '.', ''),6, '')) AS natureza_despesa
                , recurso.cod_fonte AS cod_font_recursos
                , SUM(mapa_item_dotacao.vl_dotacao) AS vl_recurso
            
            FROM licitacao.licitacao
            
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN licitacao.criterio_julgamento
              ON criterio_julgamento.cod_criterio = licitacao.cod_criterio
            
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             AND (
                    SELECT edital_anulado.num_edital FROM licitacao.edital_anulado
                    WHERE edital_anulado.num_edital=edital.num_edital
                    AND edital_anulado.exercicio=edital.exercicio
                 ) IS NULL
             
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
            
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.tipo_licitacao
              ON tipo_licitacao.cod_tipo_licitacao = licitacao.cod_tipo_licitacao
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
            
            JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
            JOIN compras.julgamento
              ON julgamento.exercicio = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao
             
            JOIN compras.julgamento_item
              ON julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             
            JOIN compras.cotacao_fornecedor_item
              ON cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
             AND cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
             AND cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
             AND cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
             AND cotacao_fornecedor_item.lote = julgamento_item.lote
             
            JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
            
            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND (
                    SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                    WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                    AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                    AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                    AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                    AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                    AND homologacao.cod_item=homologacao_anulada.cod_item
                 ) IS NULL
            
            JOIN compras.solicitacao_homologada
              ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
             AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
             AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao
            
            JOIN compras.solicitacao_homologada_reserva
              ON solicitacao_homologada_reserva.exercicio=solicitacao_homologada.exercicio
             AND solicitacao_homologada_reserva.cod_entidade=solicitacao_homologada.cod_entidade
             AND solicitacao_homologada_reserva.cod_solicitacao=solicitacao_homologada.cod_solicitacao
             AND solicitacao_homologada_reserva.cod_item=homologacao.cod_item
            
            JOIN orcamento.despesa
              ON despesa.exercicio = solicitacao_homologada_reserva.exercicio
             AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa
            
            JOIN orcamento.conta_despesa
              ON conta_despesa.exercicio = despesa.exercicio
             AND conta_despesa.cod_conta = despesa.cod_conta

	    JOIN compras.mapa_item_dotacao
              ON mapa_item_dotacao.exercicio=solicitacao_homologada.exercicio
             AND mapa_item_dotacao.cod_entidade=solicitacao_homologada.cod_entidade
             AND mapa_item_dotacao.cod_solicitacao=solicitacao_homologada.cod_solicitacao
             AND mapa_item_dotacao.cod_item=homologacao.cod_item
             AND mapa_item_dotacao.cod_mapa=mapa.cod_mapa
             AND mapa_item_dotacao.cod_despesa=despesa.cod_despesa
            
            JOIN orcamento.despesa_acao
              ON despesa_acao.cod_despesa = despesa.cod_despesa
             AND despesa_acao.exercicio_despesa = despesa.exercicio
            
            JOIN orcamento.recurso
              ON recurso.cod_recurso = despesa.cod_recurso
             AND recurso.exercicio = despesa.exercicio
            
            JOIN administracao.configuracao_entidade
              ON configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND configuracao_entidade.cod_modulo = 55
             AND configuracao_entidade.exercicio = licitacao.exercicio
             AND configuracao_entidade.cod_entidade = licitacao.cod_entidade
             
            JOIN administracao.configuracao_entidade AS orgao_despesa
              ON orgao_despesa.parametro = 'tcemg_codigo_orgao_entidade_sicom'
             AND orgao_despesa.cod_modulo = 55
             AND orgao_despesa.exercicio = despesa.exercicio
             AND orgao_despesa.cod_entidade = despesa.cod_entidade
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
            JOIN ( SELECT num_documento, numcgm, tipo_documento
                    FROM (
                            SELECT cpf AS num_documento, numcgm, 1 AS tipo_documento
                            FROM sw_cgm_pessoa_fisica
                             
                            UNION
                            
                            SELECT cnpj AS num_documento, numcgm, 2 AS tipo_documento
                            FROM sw_cgm_pessoa_juridica
                         ) AS tabela
                    GROUP BY numcgm, num_documento, tipo_documento
                 ) AS documento_pessoa
              ON documento_pessoa.numcgm = responsavel.numcgm
            LEFT JOIN licitacao.contrato_licitacao
                   ON contrato_licitacao.cod_licitacao=licitacao.cod_licitacao
                  AND contrato_licitacao.cod_modalidade=licitacao.cod_modalidade
                  AND contrato_licitacao.cod_entidade=licitacao.cod_entidade
                  AND contrato_licitacao.exercicio_licitacao=licitacao.exercicio
            
            LEFT JOIN licitacao.contrato
                   ON contrato.num_contrato=contrato_licitacao.num_contrato
                  AND contrato.cod_entidade=contrato_licitacao.cod_entidade
                  AND contrato.exercicio=contrato_licitacao.exercicio
              
            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
              AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
              AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND licitacao.cod_modalidade NOT IN (8,9)
              AND NOT EXISTS( SELECT 1
			       FROM licitacao.licitacao_anulada
			      WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
			        AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                AND licitacao_anulada.exercicio = licitacao.exercicio
                           )
              
            GROUP BY tipo_registro, cod_orgao_resp, cod_unidade_resp, num_processo_licitatorio, licitacao.exercicio,
            modalidade.cod_modalidade, sw_processo.timestamp, edital.dt_aprovacao_juridico, edital.num_edital, edital.exercicio,
            criterio_julgamento.cod_criterio, tipo_objeto.cod_tipo_objeto, objeto.descricao, mapa.cod_tipo_licitacao, fornecedor.tipo,
            contrato.inicio_execucao, contrato.fim_execucao, licitacao.cod_licitacao, licitacao.cod_modalidade, licitacao.cod_entidade,
            despesa.num_orgao, despesa.cod_funcao, despesa.cod_subfuncao, despesa.cod_programa, despesa_acao.cod_acao,
            conta_despesa.cod_estrutural, recurso.cod_fonte, despesa.vl_original, orgao_despesa.valor, cod_unidade

            ORDER BY licitacao.cod_licitacao, licitacao.cod_modalidade, num_processo_licitatorio, cod_unidade_resp
        ";
        return $stSql;
    }
    
    public function __destruct(){}
    
}
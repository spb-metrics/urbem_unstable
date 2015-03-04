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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGODSI extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaDetalhamento10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento10()
    {
        $stSql = "  SELECT DISTINCT 
                                10 AS tipo_registro
                                , licitacao.num_orgao AS cod_orgao
                                , licitacao.num_unidade AS cod_unidade
                                , sw_processo.cod_processo AS num_processo
                                , sw_processo.ano_exercicio AS ano_exercicio_processo
                                , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                                       WHEN modalidade.cod_modalidade = 9 THEN 2
                                 END AS tipo_processo
                                , TO_CHAR(licitacao.timestamp,'dd/mm/yyyy') AS dt_abertura
                                , CASE WHEN tipo_objeto.cod_tipo_objeto = 1 THEN 2
                                       WHEN tipo_objeto.cod_tipo_objeto = 2 THEN 1
                                       WHEN tipo_objeto.cod_tipo_objeto = 3 THEN 3
                                       WHEN tipo_objeto.cod_tipo_objeto = 4 THEN 3
                                 END AS natureza_objeto
                                , objeto.descricao AS objeto
                                , justificativa_razao.justificativa AS justificativa
                                , justificativa_razao.razao AS razao
                                , TO_CHAR(publicacao_edital.data_publicacao,'dd/mm/yyyy')  AS dt_publicacao_termo_ratificacao
                                , veiculo.nom_cgm AS veiculo_publicacao
                                , 0 AS numero_sequencial
                 
                    FROM licitacao.licitacao
                    
                    JOIN compras.objeto
                      ON objeto.cod_objeto = licitacao.cod_objeto
                      
                    JOIN sw_processo
                      ON sw_processo.cod_processo = licitacao.cod_processo
                     AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                     
                    JOIN compras.modalidade
                      ON modalidade.cod_modalidade = licitacao.cod_modalidade
                      
                    JOIN compras.tipo_objeto
                      ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                      
                    JOIN licitacao.edital
                      ON edital.cod_licitacao = licitacao.cod_licitacao
                     AND edital.cod_modalidade = licitacao.cod_modalidade
                     AND edital.cod_entidade = licitacao.cod_entidade
                     AND edital.exercicio_licitacao = licitacao.exercicio
                     
                    JOIN licitacao.publicacao_edital
                      ON publicacao_edital.num_edital = edital.num_edital
                     AND publicacao_edital.exercicio = edital.exercicio
                     
                    JOIN licitacao.veiculos_publicidade
                      ON veiculos_publicidade.numcgm = publicacao_edital.numcgm
                      
                    JOIN sw_cgm AS veiculo
                      ON veiculo.numcgm = veiculos_publicidade.numcgm
                      
                    JOIN compras.mapa
                        ON mapa.exercicio = licitacao.exercicio_mapa
                        AND mapa.cod_mapa = licitacao.cod_mapa     
        
                    JOIN compras.mapa_cotacao
                        ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                        AND mapa.cod_mapa = mapa_cotacao.cod_mapa
                        
                    JOIN compras.julgamento
                        ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
                        AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                        
                    JOIN compras.julgamento_item
                        ON  julgamento_item.exercicio = julgamento.exercicio
                        AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                        AND julgamento_item.ordem = 1
        
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
                                     
                    JOIN compras.cotacao_fornecedor_item
                         ON julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                        AND julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                        AND julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                        AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                        AND julgamento_item.lote = cotacao_fornecedor_item.lote
                                    
                    LEFT JOIN licitacao.justificativa_razao
                         ON justificativa_razao.cod_entidade = licitacao.cod_entidade
                        AND justificativa_razao.cod_licitacao = licitacao.cod_licitacao
                        AND justificativa_razao.exercicio = licitacao.exercicio
                        AND justificativa_razao.cod_modalidade = licitacao.cod_modalidade
        
                    WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                    AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
                    AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                    AND modalidade.cod_modalidade IN (8,9)
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
            --------------------- 1
            
                SELECT 11 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                    WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , 1 AS tipo_resp
                 , responsavel_dispensa.cpf AS num_cpf_responsavel
                 , responsavel_dispensa.nom_cgm AS nome_responsavel
                 , responsavel_dispensa.logradouro AS logradouro
                 , responsavel_dispensa.bairro AS setor
                 , responsavel_dispensa.nom_municipio AS cidade
                 , responsavel_dispensa.cep AS cep
                 , responsavel_dispensa.sigla_uf AS uf 
            
                 , CASE WHEN responsavel_dispensa.fone_residencial = ''
                    THEN CASE WHEN responsavel_dispensa.fone_comercial = ''
                          THEN CASE WHEN responsavel_dispensa.fone_celular = ''
                                THEN ''
                                ELSE responsavel_dispensa.fone_celular
                               END
                          ELSE responsavel_dispensa.fone_comercial
                         END
                    ELSE responsavel_dispensa.fone_residencial
                 END AS telefone
            
                 , CASE WHEN responsavel_dispensa.e_mail = ''
                    THEN CASE WHEN responsavel_dispensa.e_mail_adcional = ''
                          THEN ''
                          ELSE responsavel_dispensa.e_mail_adcional
                         END
                    ELSE responsavel_dispensa.e_mail
                 END AS email
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
                FROM licitacao.licitacao
                
                JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
                  
                JOIN sw_processo
                  ON sw_processo.cod_processo = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                 
                JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
                  
                JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                  
                JOIN licitacao.edital
                  ON edital.cod_licitacao = licitacao.cod_licitacao
                 AND edital.cod_modalidade = licitacao.cod_modalidade
                 AND edital.cod_entidade = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 
                JOIN licitacao.publicacao_edital
                  ON publicacao_edital.num_edital = edital.num_edital
                 AND publicacao_edital.exercicio = edital.exercicio
            
                JOIN tcmgo.responsavel_licitacao_dispensa AS responsavel
                  ON responsavel.exercicio      = licitacao.exercicio
                 AND responsavel.cod_entidade   = licitacao.cod_entidade
                 AND responsavel.cod_modalidade = licitacao.cod_modalidade
                 AND responsavel.cod_licitacao  = licitacao.cod_licitacao
            
                JOIN ( SELECT sw_cgm.nom_cgm
                    , sw_cgm.numcgm
                    , sw_cgm_pessoa_fisica.cpf
                    , sw_cgm.logradouro
                    , sw_cgm.bairro
                    , sw_cgm.cep
                    , sw_municipio.nom_municipio
                    , sw_uf.sigla_uf
                    , sw_cgm.fone_residencial
                    , sw_cgm.fone_celular
                    , sw_cgm.fone_comercial
                    , sw_cgm.e_mail
                    , sw_cgm.e_mail_adcional
                     FROM sw_cgm
                     JOIN sw_cgm_pessoa_fisica
                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                     JOIN sw_municipio
                       ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                      AND sw_municipio.cod_uf = sw_cgm.cod_uf
                     JOIN sw_uf
                       ON sw_uf.cod_uf = sw_municipio.cod_uf
                      
                ) AS responsavel_dispensa
                  ON responsavel_dispensa.numcgm = responsavel.cgm_resp_abertura_disp
                  
                WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                  AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
                  AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                  AND modalidade.cod_modalidade IN (8,9)
            
            --------------------- 2
            UNION
            
                SELECT 11 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                    WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , 2 AS tipo_resp
                 , responsavel_dispensa.cpf AS num_cpf_responsavel
                 , responsavel_dispensa.nom_cgm AS nome_responsavel
                 , responsavel_dispensa.logradouro AS logradouro
                 , responsavel_dispensa.bairro AS setor
                 , responsavel_dispensa.nom_municipio AS cidade
                 , responsavel_dispensa.cep AS cep
                 , responsavel_dispensa.sigla_uf AS uf 
            
                 , CASE WHEN responsavel_dispensa.fone_residencial = ''
                    THEN CASE WHEN responsavel_dispensa.fone_comercial = ''
                          THEN CASE WHEN responsavel_dispensa.fone_celular = ''
                                THEN ''
                                ELSE responsavel_dispensa.fone_celular
                               END
                          ELSE responsavel_dispensa.fone_comercial
                         END
                    ELSE responsavel_dispensa.fone_residencial
                 END AS telefone
            
                 , CASE WHEN responsavel_dispensa.e_mail = ''
                    THEN CASE WHEN responsavel_dispensa.e_mail_adcional = ''
                          THEN ''
                          ELSE responsavel_dispensa.e_mail_adcional
                         END
                    ELSE responsavel_dispensa.e_mail
                 END AS email
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
                FROM licitacao.licitacao
                
                JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
                  
                JOIN sw_processo
                  ON sw_processo.cod_processo = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                 
                JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
                  
                JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                  
                JOIN licitacao.edital
                  ON edital.cod_licitacao = licitacao.cod_licitacao
                 AND edital.cod_modalidade = licitacao.cod_modalidade
                 AND edital.cod_entidade = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 
                JOIN licitacao.publicacao_edital
                  ON publicacao_edital.num_edital = edital.num_edital
                 AND publicacao_edital.exercicio = edital.exercicio
                 
                JOIN tcmgo.responsavel_licitacao_dispensa AS responsavel
                  ON responsavel.exercicio      = licitacao.exercicio
                 AND responsavel.cod_entidade   = licitacao.cod_entidade
                 AND responsavel.cod_modalidade = licitacao.cod_modalidade
                 AND responsavel.cod_licitacao  = licitacao.cod_licitacao
            
                JOIN ( SELECT sw_cgm.nom_cgm
                    , sw_cgm.numcgm
                    , sw_cgm_pessoa_fisica.cpf
                    , sw_cgm.logradouro
                    , sw_cgm.bairro
                    , sw_cgm.cep
                    , sw_municipio.nom_municipio
                    , sw_uf.sigla_uf
                    , sw_cgm.fone_residencial
                    , sw_cgm.fone_celular
                    , sw_cgm.fone_comercial
                    , sw_cgm.e_mail
                    , sw_cgm.e_mail_adcional
                     FROM sw_cgm
                     JOIN sw_cgm_pessoa_fisica
                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                     JOIN sw_municipio
                       ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                      AND sw_municipio.cod_uf = sw_cgm.cod_uf
                     JOIN sw_uf
                       ON sw_uf.cod_uf = sw_municipio.cod_uf
                      
                ) AS responsavel_dispensa
                  ON responsavel_dispensa.numcgm = responsavel.cgm_resp_cotacao_precos
                  
            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)
            
            --------------------- 3
            UNION
            
                SELECT 11 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                    WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , 3 AS tipo_resp
                 , responsavel_dispensa.cpf AS num_cpf_responsavel
                 , responsavel_dispensa.nom_cgm AS nome_responsavel
                 , responsavel_dispensa.logradouro AS logradouro
                 , responsavel_dispensa.bairro AS setor
                 , responsavel_dispensa.nom_municipio AS cidade
                 , responsavel_dispensa.cep AS cep
                 , responsavel_dispensa.sigla_uf AS uf 
            
                 , CASE WHEN responsavel_dispensa.fone_residencial = ''
                    THEN CASE WHEN responsavel_dispensa.fone_comercial = ''
                          THEN CASE WHEN responsavel_dispensa.fone_celular = ''
                                THEN ''
                                ELSE responsavel_dispensa.fone_celular
                               END
                          ELSE responsavel_dispensa.fone_comercial
                         END
                    ELSE responsavel_dispensa.fone_residencial
                 END AS telefone
            
                 , CASE WHEN responsavel_dispensa.e_mail = ''
                    THEN CASE WHEN responsavel_dispensa.e_mail_adcional = ''
                          THEN ''
                          ELSE responsavel_dispensa.e_mail_adcional
                         END
                    ELSE responsavel_dispensa.e_mail
                 END AS email
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
                FROM licitacao.licitacao
                
                JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
                  
                JOIN sw_processo
                  ON sw_processo.cod_processo = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                 
                JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
                  
                JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                  
                JOIN licitacao.edital
                  ON edital.cod_licitacao = licitacao.cod_licitacao
                 AND edital.cod_modalidade = licitacao.cod_modalidade
                 AND edital.cod_entidade = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 
                JOIN licitacao.publicacao_edital
                  ON publicacao_edital.num_edital = edital.num_edital
                 AND publicacao_edital.exercicio = edital.exercicio
                 
                JOIN tcmgo.responsavel_licitacao_dispensa AS responsavel
                  ON responsavel.exercicio      = licitacao.exercicio
                 AND responsavel.cod_entidade   = licitacao.cod_entidade
                 AND responsavel.cod_modalidade = licitacao.cod_modalidade
                 AND responsavel.cod_licitacao  = licitacao.cod_licitacao
            
                JOIN ( SELECT sw_cgm.nom_cgm
                    , sw_cgm.numcgm
                    , sw_cgm_pessoa_fisica.cpf
                    , sw_cgm.logradouro
                    , sw_cgm.bairro
                    , sw_cgm.cep
                    , sw_municipio.nom_municipio
                    , sw_uf.sigla_uf
                    , sw_cgm.fone_residencial
                    , sw_cgm.fone_celular
                    , sw_cgm.fone_comercial
                    , sw_cgm.e_mail
                    , sw_cgm.e_mail_adcional
                     FROM sw_cgm
                     JOIN sw_cgm_pessoa_fisica
                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                     JOIN sw_municipio
                       ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                      AND sw_municipio.cod_uf = sw_cgm.cod_uf
                     JOIN sw_uf
                       ON sw_uf.cod_uf = sw_municipio.cod_uf
                      
                ) AS responsavel_dispensa
                  ON responsavel_dispensa.numcgm = responsavel.cgm_resp_recurso
                  
            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)
              
            --------------------- 4
            UNION
            
                SELECT 11 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                    WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , 4 AS tipo_resp
                 , responsavel_dispensa.cpf AS num_cpf_responsavel
                 , responsavel_dispensa.nom_cgm AS nome_responsavel
                 , responsavel_dispensa.logradouro AS logradouro
                 , responsavel_dispensa.bairro AS setor
                 , responsavel_dispensa.nom_municipio AS cidade
                 , responsavel_dispensa.cep AS cep
                 , responsavel_dispensa.sigla_uf AS uf 
            
                 , CASE WHEN responsavel_dispensa.fone_residencial = ''
                    THEN CASE WHEN responsavel_dispensa.fone_comercial = ''
                          THEN CASE WHEN responsavel_dispensa.fone_celular = ''
                                THEN ''
                                ELSE responsavel_dispensa.fone_celular
                               END
                          ELSE responsavel_dispensa.fone_comercial
                         END
                    ELSE responsavel_dispensa.fone_residencial
                 END AS telefone
            
                 , CASE WHEN responsavel_dispensa.e_mail = ''
                    THEN CASE WHEN responsavel_dispensa.e_mail_adcional = ''
                          THEN ''
                          ELSE responsavel_dispensa.e_mail_adcional
                         END
                    ELSE responsavel_dispensa.e_mail
                 END AS email
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
                FROM licitacao.licitacao
                
                JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
                  
                JOIN sw_processo
                  ON sw_processo.cod_processo = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                 
                JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
                  
                JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                  
                JOIN licitacao.edital
                  ON edital.cod_licitacao = licitacao.cod_licitacao
                 AND edital.cod_modalidade = licitacao.cod_modalidade
                 AND edital.cod_entidade = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 
                JOIN licitacao.publicacao_edital
                  ON publicacao_edital.num_edital = edital.num_edital
                 AND publicacao_edital.exercicio = edital.exercicio
                 
                JOIN tcmgo.responsavel_licitacao_dispensa AS responsavel
                  ON responsavel.exercicio      = licitacao.exercicio
                 AND responsavel.cod_entidade   = licitacao.cod_entidade
                 AND responsavel.cod_modalidade = licitacao.cod_modalidade
                 AND responsavel.cod_licitacao  = licitacao.cod_licitacao
            
                JOIN ( SELECT sw_cgm.nom_cgm
                    , sw_cgm.numcgm
                    , sw_cgm_pessoa_fisica.cpf
                    , sw_cgm.logradouro
                    , sw_cgm.bairro
                    , sw_cgm.cep
                    , sw_municipio.nom_municipio
                    , sw_uf.sigla_uf
                    , sw_cgm.fone_residencial
                    , sw_cgm.fone_celular
                    , sw_cgm.fone_comercial
                    , sw_cgm.e_mail
                    , sw_cgm.e_mail_adcional
                     FROM sw_cgm
                     JOIN sw_cgm_pessoa_fisica
                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                     JOIN sw_municipio
                       ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                      AND sw_municipio.cod_uf = sw_cgm.cod_uf
                     JOIN sw_uf
                       ON sw_uf.cod_uf = sw_municipio.cod_uf
                      
                ) AS responsavel_dispensa
                  ON responsavel_dispensa.numcgm = responsavel.cgm_resp_ratificacao
            
            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)
              
            --------------------- 5
            UNION
            
                SELECT 11 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                    WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , 5 AS tipo_resp
                 , responsavel_dispensa.cpf AS num_cpf_responsavel
                 , responsavel_dispensa.nom_cgm AS nome_responsavel
                 , responsavel_dispensa.logradouro AS logradouro
                 , responsavel_dispensa.bairro AS setor
                 , responsavel_dispensa.nom_municipio AS cidade
                 , responsavel_dispensa.cep AS cep
                 , responsavel_dispensa.sigla_uf AS uf 
            
                 , CASE WHEN responsavel_dispensa.fone_residencial = ''
                    THEN CASE WHEN responsavel_dispensa.fone_comercial = ''
                          THEN CASE WHEN responsavel_dispensa.fone_celular = ''
                                THEN ''
                                ELSE responsavel_dispensa.fone_celular
                               END
                          ELSE responsavel_dispensa.fone_comercial
                         END
                    ELSE responsavel_dispensa.fone_residencial
                 END AS telefone
            
                 , CASE WHEN responsavel_dispensa.e_mail = ''
                    THEN CASE WHEN responsavel_dispensa.e_mail_adcional = ''
                          THEN ''
                          ELSE responsavel_dispensa.e_mail_adcional
                         END
                    ELSE responsavel_dispensa.e_mail
                 END AS email
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
                FROM licitacao.licitacao
                
                JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
                  
                JOIN sw_processo
                  ON sw_processo.cod_processo = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                 
                JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
                  
                JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                  
                JOIN licitacao.edital
                  ON edital.cod_licitacao = licitacao.cod_licitacao
                 AND edital.cod_modalidade = licitacao.cod_modalidade
                 AND edital.cod_entidade = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 
                JOIN licitacao.publicacao_edital
                  ON publicacao_edital.num_edital = edital.num_edital
                 AND publicacao_edital.exercicio = edital.exercicio
                 
                JOIN tcmgo.responsavel_licitacao_dispensa AS responsavel
                  ON responsavel.exercicio      = licitacao.exercicio
                 AND responsavel.cod_entidade   = licitacao.cod_entidade
                 AND responsavel.cod_modalidade = licitacao.cod_modalidade
                 AND responsavel.cod_licitacao  = licitacao.cod_licitacao
            
                JOIN ( SELECT sw_cgm.nom_cgm
                    , sw_cgm.numcgm
                    , sw_cgm_pessoa_fisica.cpf
                    , sw_cgm.logradouro
                    , sw_cgm.bairro
                    , sw_cgm.cep
                    , sw_municipio.nom_municipio
                    , sw_uf.sigla_uf
                    , sw_cgm.fone_residencial
                    , sw_cgm.fone_celular
                    , sw_cgm.fone_comercial
                    , sw_cgm.e_mail
                    , sw_cgm.e_mail_adcional
                     FROM sw_cgm
                     JOIN sw_cgm_pessoa_fisica
                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                     JOIN sw_municipio
                       ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                      AND sw_municipio.cod_uf = sw_cgm.cod_uf
                     JOIN sw_uf
                       ON sw_uf.cod_uf = sw_municipio.cod_uf
                      
                ) AS responsavel_dispensa
                  ON responsavel_dispensa.numcgm = responsavel.cgm_resp_publicacao_orgao
            
            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)
            
            --------------------- 6
            UNION
            
                SELECT 11 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                    WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , 6 AS tipo_resp
                 , responsavel_dispensa.cpf AS num_cpf_responsavel
                 , responsavel_dispensa.nom_cgm AS nome_responsavel
                 , responsavel_dispensa.logradouro AS logradouro
                 , responsavel_dispensa.bairro AS setor
                 , responsavel_dispensa.nom_municipio AS cidade
                 , responsavel_dispensa.cep AS cep
                 , responsavel_dispensa.sigla_uf AS uf 
            
                 , CASE WHEN responsavel_dispensa.fone_residencial = ''
                    THEN CASE WHEN responsavel_dispensa.fone_comercial = ''
                          THEN CASE WHEN responsavel_dispensa.fone_celular = ''
                                THEN ''
                                ELSE responsavel_dispensa.fone_celular
                               END
                          ELSE responsavel_dispensa.fone_comercial
                         END
                    ELSE responsavel_dispensa.fone_residencial
                 END AS telefone
            
                 , CASE WHEN responsavel_dispensa.e_mail = ''
                    THEN CASE WHEN responsavel_dispensa.e_mail_adcional = ''
                          THEN ''
                          ELSE responsavel_dispensa.e_mail_adcional
                         END
                    ELSE responsavel_dispensa.e_mail
                 END AS email
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
                FROM licitacao.licitacao
                
                JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
                  
                JOIN sw_processo
                  ON sw_processo.cod_processo = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                 
                JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
                  
                JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                  
                JOIN licitacao.edital
                  ON edital.cod_licitacao = licitacao.cod_licitacao
                 AND edital.cod_modalidade = licitacao.cod_modalidade
                 AND edital.cod_entidade = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 
                JOIN licitacao.publicacao_edital
                  ON publicacao_edital.num_edital = edital.num_edital
                 AND publicacao_edital.exercicio = edital.exercicio
                 
                JOIN tcmgo.responsavel_licitacao_dispensa AS responsavel
                  ON responsavel.exercicio      = licitacao.exercicio
                 AND responsavel.cod_entidade   = licitacao.cod_entidade
                 AND responsavel.cod_modalidade = licitacao.cod_modalidade
                 AND responsavel.cod_licitacao  = licitacao.cod_licitacao
            
                JOIN ( SELECT sw_cgm.nom_cgm
                    , sw_cgm.numcgm
                    , sw_cgm_pessoa_fisica.cpf
                    , sw_cgm.logradouro
                    , sw_cgm.bairro
                    , sw_cgm.cep
                    , sw_municipio.nom_municipio
                    , sw_uf.sigla_uf
                    , sw_cgm.fone_residencial
                    , sw_cgm.fone_celular
                    , sw_cgm.fone_comercial
                    , sw_cgm.e_mail
                    , sw_cgm.e_mail_adcional
                     FROM sw_cgm
                     JOIN sw_cgm_pessoa_fisica
                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                     JOIN sw_municipio
                       ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                      AND sw_municipio.cod_uf = sw_cgm.cod_uf
                     JOIN sw_uf
                       ON sw_uf.cod_uf = sw_municipio.cod_uf
                      
                ) AS responsavel_dispensa
                  ON responsavel_dispensa.numcgm = responsavel.cgm_resp_parecer_juridico
            
            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)
            
            --------------------- 7
            UNION
            
                SELECT 11 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                    WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , 7 AS tipo_resp
                 , responsavel_dispensa.cpf AS num_cpf_responsavel
                 , responsavel_dispensa.nom_cgm AS nome_responsavel
                 , responsavel_dispensa.logradouro AS logradouro
                 , responsavel_dispensa.bairro AS setor
                 , responsavel_dispensa.nom_municipio AS cidade
                 , responsavel_dispensa.cep AS cep
                 , responsavel_dispensa.sigla_uf AS uf 
            
                 , CASE WHEN responsavel_dispensa.fone_residencial = ''
                    THEN CASE WHEN responsavel_dispensa.fone_comercial = ''
                          THEN CASE WHEN responsavel_dispensa.fone_celular = ''
                                THEN ''
                                ELSE responsavel_dispensa.fone_celular
                               END
                          ELSE responsavel_dispensa.fone_comercial
                         END
                    ELSE responsavel_dispensa.fone_residencial
                 END AS telefone
            
                 , CASE WHEN responsavel_dispensa.e_mail = ''
                    THEN CASE WHEN responsavel_dispensa.e_mail_adcional = ''
                          THEN ''
                          ELSE responsavel_dispensa.e_mail_adcional
                         END
                    ELSE responsavel_dispensa.e_mail
                 END AS email
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
                FROM licitacao.licitacao
                
                JOIN compras.objeto
                  ON objeto.cod_objeto = licitacao.cod_objeto
                  
                JOIN sw_processo
                  ON sw_processo.cod_processo = licitacao.cod_processo
                 AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                 
                JOIN compras.modalidade
                  ON modalidade.cod_modalidade = licitacao.cod_modalidade
                  
                JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                  
                JOIN licitacao.edital
                  ON edital.cod_licitacao = licitacao.cod_licitacao
                 AND edital.cod_modalidade = licitacao.cod_modalidade
                 AND edital.cod_entidade = licitacao.cod_entidade
                 AND edital.exercicio_licitacao = licitacao.exercicio
                 
                JOIN licitacao.publicacao_edital
                  ON publicacao_edital.num_edital = edital.num_edital
                 AND publicacao_edital.exercicio = edital.exercicio
                 
                JOIN tcmgo.responsavel_licitacao_dispensa AS responsavel
                  ON responsavel.exercicio      = licitacao.exercicio
                 AND responsavel.cod_entidade   = licitacao.cod_entidade
                 AND responsavel.cod_modalidade = licitacao.cod_modalidade
                 AND responsavel.cod_licitacao  = licitacao.cod_licitacao
            
                JOIN ( SELECT sw_cgm.nom_cgm
                    , sw_cgm.numcgm
                    , sw_cgm_pessoa_fisica.cpf
                    , sw_cgm.logradouro
                    , sw_cgm.bairro
                    , sw_cgm.cep
                    , sw_municipio.nom_municipio
                    , sw_uf.sigla_uf
                    , sw_cgm.fone_residencial
                    , sw_cgm.fone_celular
                    , sw_cgm.fone_comercial
                    , sw_cgm.e_mail
                    , sw_cgm.e_mail_adcional
                     FROM sw_cgm
                     JOIN sw_cgm_pessoa_fisica
                       ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                     JOIN sw_municipio
                       ON sw_municipio.cod_municipio = sw_cgm.cod_municipio
                      AND sw_municipio.cod_uf = sw_cgm.cod_uf
                     JOIN sw_uf
                       ON sw_uf.cod_uf = sw_municipio.cod_uf
                      
                ) AS responsavel_dispensa
                  ON responsavel_dispensa.numcgm = responsavel.cgm_resp_parecer_outro

            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)

            ORDER BY tipo_resp
        ";
            
        return $stSql;
    }
    
    public function recuperaDetalhamento12(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamento12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamento12()
    {
        $stSql = "  SELECT DISTINCT 
                                12 AS tipo_registro
                                , licitacao.num_orgao AS cod_orgao
                                , licitacao.num_unidade AS cod_unidade
                                , sw_processo.cod_processo AS num_processo
                                , sw_processo.ano_exercicio AS ano_exercicio_processo
                                , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                                       WHEN modalidade.cod_modalidade = 9 THEN 2
                                  END AS tipo_processo
                                , mapa_item.lote AS num_lote
                                , mapa_item.cod_item AS num_item
                                , mapa_item.vl_total AS vl_cot_precos_unitario
                                , catalogo_item.descricao::varchar(250) as desc_item
                                , '' AS brancos
                                , 0 AS nro_sequencial
                 
                    FROM licitacao.licitacao
            
                    JOIN compras.objeto
                      ON objeto.cod_objeto = licitacao.cod_objeto
                      
                    JOIN sw_processo
                      ON sw_processo.cod_processo = licitacao.cod_processo
                     AND sw_processo.ano_exercicio = licitacao.exercicio_processo
                     
                    JOIN compras.modalidade
                      ON modalidade.cod_modalidade = licitacao.cod_modalidade
                      
                    JOIN compras.tipo_objeto
                      ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                      
                    JOIN licitacao.edital
                      ON edital.cod_licitacao = licitacao.cod_licitacao
                     AND edital.cod_modalidade = licitacao.cod_modalidade
                     AND edital.cod_entidade = licitacao.cod_entidade
                     AND edital.exercicio_licitacao = licitacao.exercicio
                     
                    JOIN licitacao.publicacao_edital
                      ON publicacao_edital.num_edital = edital.num_edital
                     AND publicacao_edital.exercicio = edital.exercicio
                     
                    JOIN sw_cgm AS responsavel
                      ON responsavel.numcgm = edital.responsavel_juridico
                      
                    JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = responsavel.numcgm
                      
                    JOIN sw_municipio
                      ON sw_municipio.cod_municipio = responsavel.cod_municipio
                     AND sw_municipio.cod_uf = responsavel.cod_uf
                     
                    JOIN sw_uf
                      ON sw_uf.cod_uf = sw_municipio.cod_uf
                      
                    JOIN compras.mapa
                      ON mapa.exercicio = licitacao.exercicio_mapa
                     AND mapa.cod_mapa = licitacao.cod_mapa
                     
                    JOIN compras.mapa_solicitacao
                      ON mapa_solicitacao.exercicio = mapa.exercicio
                     AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
                     
                    JOIN compras.mapa_item
                      ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
                     AND mapa_item.exercicio = mapa_solicitacao.exercicio
                     AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
                     AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                     AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                     
                    JOIN compras.mapa_cotacao
                      ON mapa_cotacao.exercicio_cotacao = mapa.exercicio
                     AND mapa_cotacao.cod_mapa = mapa.cod_mapa
                     
                    JOIN compras.cotacao
                      ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
                     AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                     
                    JOIN compras.cotacao_item
                      ON cotacao_item.exercicio   = cotacao.exercicio
                     AND cotacao_item.cod_cotacao = cotacao.cod_cotacao
                    
                    JOIN almoxarifado.catalogo_item
                        ON catalogo_item.cod_item   = cotacao_item.cod_item
        
                    WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                    AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
                    AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                    AND modalidade.cod_modalidade IN (8,9)
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
            SELECT 13 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , despesa.cod_funcao AS cod_funcao
                 , despesa.cod_subfuncao AS cod_subfuncao
                 , despesa.cod_programa AS cod_programa
                 , SUBSTR(despesa.num_pao::varchar,1,1) AS natureza_acao
                 , SUBSTR(despesa.num_pao::varchar,2,3) AS num_proj_ativ
                 , SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6) AS elemento_despesa
                 , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                 END AS subelemento
                 , recurso.cod_recurso AS cod_fonte_recurso
                 , despesa.vl_original AS valor_recurso
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
            FROM licitacao.licitacao
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
            JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
            JOIN sw_cgm AS responsavel
              ON responsavel.numcgm = edital.responsavel_juridico
              
            JOIN sw_cgm_pessoa_fisica
              ON sw_cgm_pessoa_fisica.numcgm = responsavel.numcgm
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
            JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = mapa.exercicio
             AND mapa_cotacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
            JOIN empenho.item_pre_empenho_julgamento
              ON item_pre_empenho_julgamento.exercicio = cotacao.exercicio
             AND item_pre_empenho_julgamento.cod_cotacao = cotacao.cod_cotacao
             
            JOIN empenho.item_pre_empenho
              ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
             AND item_pre_empenho.exercicio = item_pre_empenho_julgamento.exercicio
             AND item_pre_empenho.num_item = item_pre_empenho.num_item
             
            JOIN empenho.pre_empenho
              ON pre_empenho.exercicio = item_pre_empenho.exercicio
             AND pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
             
            JOIN empenho.pre_empenho_despesa
              ON pre_empenho_despesa.exercicio  = pre_empenho.exercicio
             AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
             
            JOIN orcamento.despesa
              ON despesa.exercicio = pre_empenho_despesa.exercicio
             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
             
            JOIN orcamento.conta_despesa
              ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
             AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
             
       LEFT JOIN tcmgo.elemento_de_para
              ON elemento_de_para.cod_conta = conta_despesa.cod_conta
             AND elemento_de_para.exercicio = conta_despesa.exercicio
             
            JOIN orcamento.recurso
              ON recurso.exercicio = despesa.exercicio
             AND recurso.cod_recurso = despesa.cod_recurso
              
            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)
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
            SELECT 14 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , documento_pessoa.tipo_documento AS tipo_documento
                 , documento_pessoa.num_documento AS num_documento
                 , mapa_item.lote AS num_lote
                 , mapa_item.cod_item AS num_item
                 , responsavel.nom_cgm AS nom_razao_social
                 , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                 , sw_uf.sigla_uf AS uf_inscricao_estadual
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularida_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularida_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_cndt
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_cndt
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_cndt
                 , mapa_item.quantidade AS quantidade
                 , mapa_item.vl_total AS valor_item
                 , '' AS brancos
                 , 0 AS numero_sequencial
                 
            FROM licitacao.licitacao
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
            JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
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
              
       LEFT JOIN sw_cgm_pessoa_juridica
              ON sw_cgm_pessoa_juridica.numcgm = responsavel.numcgm
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
            JOIN licitacao.licitacao_documentos
              ON licitacao_documentos.cod_licitacao = licitacao.cod_licitacao
             AND licitacao_documentos.cod_entidade = licitacao.cod_entidade
             AND licitacao_documentos.exercicio = licitacao.exercicio
             
            JOIN licitacao.documento
              ON documento.cod_documento = licitacao_documentos.cod_documento
              
            JOIN licitacao.certificacao_documentos
              ON certificacao_documentos.cod_documento = documento.cod_documento
              
            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND licitacao.timestamp BETWEEN TO_DATE('" . $this->getDado('dtInicio') . "','dd/mm/yyyy') AND TO_DATE('" . $this->getDado('dtFim') . "','dd/mm/yyyy')
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)
              AND documento.cod_documento IN (5,6,7)
              
         GROUP BY  tipo_registro
                 , cod_orgao
                 , cod_unidade
                 , num_processo
                 , ano_exercicio_processo
                 , tipo_processo
                 , tipo_documento
                 , documento_pessoa.num_documento
                 , num_lote
                 , num_item
                 , nom_razao_social
                 , num_inscricao_estadual
                 , uf_inscricao_estadual
                 , num_certidao_regularidade_inss
                 , dt_emissao_certidao_regularidade_inss
                 , dt_validade_certidao_regularida_inss
                 , num_certidao_regularidade_fgts
                 , dt_emissao_certidao_regularidade_fgts
                 , dt_validade_certidao_regularida_fgts
                 , num_cndt
                 , dt_emissao_cndt
                 , dt_validade_cndt
                 , quantidade
                 , valor_item
                 , brancos
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
            SELECT 15 AS tipo_registro
                 , licitacao.num_orgao AS cod_orgao
                 , licitacao.num_unidade AS cod_unidade
                 , sw_processo.cod_processo AS num_processo
                 , sw_processo.ano_exercicio AS ano_exercicio_processo
                 , CASE WHEN modalidade.cod_modalidade = 8 THEN 1
                        WHEN modalidade.cod_modalidade = 9 THEN 2
                 END AS tipo_processo
                 , documento_pessoa.tipo_documento AS tipo_documento
                 , documento_pessoa.num_documento AS num_documento
                 , TO_CHAR (participante_certificacao.dt_registro, 'dd/mm/yyyy') AS dt_credenciamento
                 , mapa_item.lote AS num_lote
                 , mapa_item.cod_item AS num_item
                 , responsavel.nom_cgm AS nome_razao_social
                 , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                 , sw_uf.sigla_uf AS uf_inscricao_estadual
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_certidao_regularidade_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularida_inss
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_certidao_regularidade_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularida_fgts
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN certificacao_documentos.num_certificacao ELSE 0 END AS num_cndt
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_cndt
                 , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_cndt
                 , '' AS brancos
                 , 0 AS nro_sequencial
                 
            FROM licitacao.licitacao
            
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN sw_processo
              ON sw_processo.cod_processo = licitacao.cod_processo
             AND sw_processo.ano_exercicio = licitacao.exercicio_processo
             
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN compras.tipo_objeto
              ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              
            JOIN licitacao.edital
              ON edital.cod_licitacao = licitacao.cod_licitacao
             AND edital.cod_modalidade = licitacao.cod_modalidade
             AND edital.cod_entidade = licitacao.cod_entidade
             AND edital.exercicio_licitacao = licitacao.exercicio
             
            JOIN licitacao.publicacao_edital
              ON publicacao_edital.num_edital = edital.num_edital
             AND publicacao_edital.exercicio = edital.exercicio
             
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
              
       LEFT JOIN sw_cgm_pessoa_juridica
              ON sw_cgm_pessoa_juridica.numcgm = responsavel.numcgm
              
            JOIN sw_municipio
              ON sw_municipio.cod_municipio = responsavel.cod_municipio
             AND sw_municipio.cod_uf = responsavel.cod_uf
             
            JOIN sw_uf
              ON sw_uf.cod_uf = sw_municipio.cod_uf
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
             
            JOIN licitacao.licitacao_documentos
              ON licitacao_documentos.cod_licitacao = licitacao.cod_licitacao
             AND licitacao_documentos.cod_entidade = licitacao.cod_entidade
             AND licitacao_documentos.exercicio = licitacao.exercicio
             
            JOIN licitacao.documento
              ON documento.cod_documento = licitacao_documentos.cod_documento
              
            JOIN licitacao.certificacao_documentos
              ON certificacao_documentos.cod_documento = documento.cod_documento
              
            JOIN licitacao.participante_certificacao
              ON participante_certificacao.num_certificacao = certificacao_documentos.num_certificacao
             AND participante_certificacao.exercicio = certificacao_documentos.exercicio
             AND participante_certificacao.cgm_fornecedor = certificacao_documentos.cgm_fornecedor
              
            WHERE licitacao.exercicio = '" . $this->getDado('exercicio') . "'
              AND TO_CHAR(licitacao.timestamp, 'dd/mm/yyyy') BETWEEN '" . $this->getDado('dtInicio') . "' AND '" . $this->getDado('dtFim') . "'
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND modalidade.cod_modalidade IN (8,9)
              AND documento.cod_documento IN (5,6,7)
              
         GROUP BY  tipo_registro
                 , cod_orgao
                 , cod_unidade
                 , sw_processo.cod_processo
                 , sw_processo.ano_exercicio
                 , tipo_processo
                 , documento_pessoa.tipo_documento
                 , documento_pessoa.num_documento
                 , dt_credenciamento
                 , num_lote
                 , num_item
                 , nome_razao_social
                 , num_inscricao_estadual
                 , uf_inscricao_estadual
                 , num_certidao_regularidade_inss
                 , dt_emissao_certidao_regularidade_inss
                 , dt_validade_certidao_regularida_inss
                 , num_certidao_regularidade_fgts
                 , dt_emissao_certidao_regularidade_fgts
                 , dt_validade_certidao_regularida_fgts
                 , num_cndt
                 , dt_emissao_cndt
                 , dt_validade_cndt
            ";
            
        return $stSql;
    }

}

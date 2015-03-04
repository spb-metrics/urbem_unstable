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
    * Classe de mapeamento do arquivo PRL.TXT
    * Data de Criação: 26/01/2015
    * @author Analista: Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Evandro melos
    * @package URBEM
    * @subpackage Mapeamento
    * $Revision: $
    * $Id: $
    * $Name: $
    * $Author: evandro $
    * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGOParecerLicitacao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaPareceLicitacaoRegistro10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaPareceLicitacaoRegistro10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaPareceLicitacaoRegistro10()
    {
        $stSql ="   SELECT  10 as tipo_registro
                            , LPAD(orgao.num_orgao::varchar,2,'0') as cod_orgao
                            , LPAD(licitacao.num_orgao::varchar,2, '0') AS cod_unidade
                            , licitacao.exercicio as exercicio_licitacao
                            , licitacao.exercicio::varchar||LPAD(''||licitacao.cod_entidade::varchar,2, '0')||LPAD(''||licitacao.cod_modalidade::varchar,2, '0')||LPAD(''||licitacao.cod_licitacao::varchar,4, '0') AS num_processo_licitatorio 
                            , to_char(edital.dt_aprovacao_juridico, 'ddmmyyyy') as data_parecer
                            , 2 as tipo_parecer
                            , pf.cpf as cpf
                            , sw_cgm.nom_cgm as nome_resp_parecer                            
                            , sw_cgm.logradouro as logra_res
                            , ''::varchar(20) as setor_logra
                            , sw_municipio.nom_municipio as cidade_logra
                            , sw_uf.sigla_uf as uf_cidade_logra
                            , sw_cgm.cep as cep_logra_responsavel
                            , CASE WHEN sw_cgm.fone_residencial != '' THEN
                                        sw_cgm.fone_residencial 
                                    ELSE
                                        sw_cgm.fone_celular 
                            END as fone
                            , sw_cgm.e_mail as email
                            
                    FROM licitacao.licitacao    
                    JOIN licitacao.edital
                         ON edital.exercicio_licitacao = licitacao.exercicio
                        AND edital.cod_licitacao   = licitacao.cod_licitacao
                        AND edital.cod_modalidade  = licitacao.cod_modalidade
                        AND edital.cod_entidade    = licitacao.cod_entidade
                    JOIN licitacao.cotacao_licitacao AS CL
                         ON CL.cod_licitacao       = licitacao.cod_licitacao
                        AND CL.cod_modalidade      = licitacao.cod_modalidade
                        AND CL.cod_entidade        = licitacao.cod_entidade
                        AND CL.exercicio_licitacao = licitacao.exercicio
                    JOIN licitacao.adjudicacao AS A
                         ON A.cod_licitacao        = CL.cod_licitacao
                        AND A.cod_modalidade       = CL.cod_modalidade
                        AND A.cod_entidade         = CL.cod_entidade
                        AND A.exercicio_licitacao  = CL.exercicio_licitacao
                        AND A.lote                 = CL.lote
                        AND A.cod_cotacao          = CL.cod_cotacao
                        AND A.cod_item             = CL.cod_item
                        AND A.exercicio_cotacao    = CL.exercicio_cotacao
                        AND A.cgm_fornecedor       = CL.cgm_fornecedor
                    JOIN licitacao.homologacao AS H
                         ON H.num_adjudicacao      = A.num_adjudicacao
                        AND H.cod_entidade         = A.cod_entidade
                        AND H.cod_modalidade       = A.cod_modalidade
                        AND H.cod_licitacao        = A.cod_licitacao
                        AND H.exercicio_licitacao  = A.exercicio_licitacao
                        AND H.cod_item             = A.cod_item
                        AND H.cod_cotacao          = A.cod_cotacao
                        AND H.lote                 = A.lote
                        AND H.exercicio_cotacao    = A.exercicio_cotacao
                        AND H.cgm_fornecedor       = A.cgm_fornecedor
                        AND (   SELECT num_homologacao
                                FROM licitacao.homologacao_anulada AS HANUL
                                WHERE HANUL.num_homologacao      = H.num_homologacao
                                AND HANUL.cod_licitacao        = H.cod_licitacao
                                AND HANUL.cod_modalidade       = H.cod_modalidade
                                AND HANUL.cod_entidade         = H.cod_entidade
                                AND HANUL.num_adjudicacao      = H.num_adjudicacao
                                AND HANUL.exercicio_licitacao  = H.exercicio_licitacao
                                AND HANUL.lote                 = H.lote
                                AND HANUL.cod_cotacao          = H.cod_cotacao
                                AND HANUL.cod_item             = H.cod_item
                                AND HANUL.exercicio_cotacao    = H.exercicio_cotacao
                                AND HANUL.cgm_fornecedor       = H.cgm_fornecedor
                            ) IS NULL  
                    
                    JOIN public.sw_cgm_pessoa_fisica as pf
                        ON pf.numcgm = edital.responsavel_juridico

                    JOIN sw_cgm
                        ON sw_cgm.numcgm = pf.numcgm
                        
                    JOIN sw_municipio
                        ON sw_municipio.cod_municipio   = sw_cgm.cod_municipio
                        AND sw_municipio.cod_uf         = sw_cgm.cod_uf

                    JOIN sw_uf
                        ON sw_uf.cod_uf = sw_municipio.cod_uf

                    LEFT JOIN tcmgo.orgao
                        ON orgao.num_orgao = licitacao.num_orgao
                       AND orgao.exercicio = licitacao.exercicio
               
                    WHERE H.timestamp BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                    AND licitacao.cod_modalidade NOT IN (8,9)
                    AND NOT EXISTS (SELECT 1
                                FROM licitacao.licitacao_anulada
                                WHERE licitacao_anulada.exercicio = licitacao.exercicio
                                AND licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                )
               
               GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16
            ";

        return $stSql;
    }
}

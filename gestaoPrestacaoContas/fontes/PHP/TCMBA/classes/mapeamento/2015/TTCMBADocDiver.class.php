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
/*
    * Arquivo de geracao do arquivo sertTerceiros TCM/MG
    * Data de Criação   : 11/09/2015
    * 
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Lisiane da Rosa Morais
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    * $Id: $
    * $Rev: $
    * $Author:$
    * $Date:$
    * 
*/
include_once CLA_PERSISTENTE;

class TTCMBADocDiver extends Persistente {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaDados(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaDados()
    {   $stSql = " SELECT 1 AS tp_registro
                        , de.num_unidade AS cd_unidade
                        , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                        , em.cod_empenho AS nu_empenho
		                , nota_liq_paga.data_pagamento AS dt_pagamento_empenho   
		                , nota_liq_paga.num_documento AS num_documento                   
                        , de.num_orgao AS cd_orgao
                        , op.exercicio AS dt_ano
                        , cgm_pe.tp_pessoa
                        , cgm_pe.documento
                        , cgm_pe.nom_cgm as beneficiario    
                        , nota_liq_paga.nm_documento
                        , nota_liq_paga.data_emissao
                        , nota_liq_paga.vl_pago 
                        , op.observacao AS objeto_documento     
                        , TO_CHAR(nota_liq_paga.data_pagamento, 'yyyymm') as competencia
                     FROM empenho.ordem_pagamento AS op                                                                                                  
                LEFT JOIN empenho.ordem_pagamento_anulada AS opa
                       ON op.cod_ordem     = opa.cod_ordem                                                                                        
                      AND op.exercicio     = opa.exercicio                                                                                        
                      AND op.cod_entidade = opa.cod_entidade                                                                                      
                      AND op.exercicio =  '".$this->getDado('exercicio')."'                              
                     JOIN empenho.pagamento_liquidacao as pl
                       ON op.cod_ordem    = pl.cod_ordem                                                                                          
                      AND op.cod_entidade = pl.cod_entidade                                                                                       
                      AND op.exercicio = '".$this->getDado('exercicio')."'                                
                      AND op.exercicio = pl.exercicio                                                                                          
                     JOIN empenho.nota_liquidacao as nl
                       ON pl.cod_nota              = nl.cod_nota                                             
                      AND pl.cod_entidade          = nl.cod_entidade                                         
                      AND pl.exercicio_liquidacao  = nl.exercicio        
                LEFT JOIN ( SELECT nlp.cod_entidade                                                                                           
                                 , nlp.cod_nota                                                                                               
                                 , plnlp.cod_ordem                                                                                            
                                 , plnlp.exercicio                                                                                            
                                 , nlp.exercicio as exercicio_liquidacao                                                                      
                                 , sum(coalesce(nlp.vl_pago ,0.00)) as vl_pago      
                                 , sum(coalesce(nlpa.vl_anulado ,0.00)) as vl_anulado                                                          
                                 , TO_DATE(TO_CHAR(nlp.timestamp, 'dd/mm/yyyy'),'dd/mm/yyyy') AS data_pagamento      
                                 , ptdp.num_documento
                                 , tipo_documento_pagamento.descricao AS nm_documento
                                 , TO_DATE(TO_CHAR(ptdp.timestamp, 'dd/mm/yyyy'),'dd/mm/yyyy') AS data_emissao    
                              FROM empenho.pagamento_liquidacao_nota_liquidacao_paga as plnlp   
                                 , tesouraria.pagamento as tp     
                         LEFT JOIN tcmba.pagamento_tipo_documento_pagamento as ptdp
                                ON ptdp.cod_entidade = tp.cod_entidade                                                                    
                               AND ptdp.cod_nota     = tp.cod_nota                                                                        
                               AND ptdp.exercicio    = tp.exercicio                                                            
                               AND ptdp.timestamp    = tp.timestamp  
                         LEFT JOIN tcmba.tipo_documento_pagamento 
                                ON tipo_documento_pagamento.cod_tipo = ptdp.cod_tipo
                                 , empenho.nota_liquidacao_paga as nlp                
                         LEFT JOIN (SELECT exercicio                                                                  
                                         , cod_nota                                                                   
                                         , cod_entidade                                                               
                                         , timestamp                                                                  
                                         , coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado                          
                                      FROM empenho.nota_liquidacao_paga_anulada as nlpa                               
                                  GROUP BY exercicio, cod_nota, cod_entidade, timestamp                             
                                  ) as nlpa
                                 ON nlp.exercicio    = nlpa.exercicio                                              
                                AND nlp.cod_nota     = nlpa.cod_nota             
                                AND nlp.cod_entidade = nlpa.cod_entidade         
                                AND nlp.timestamp    = nlpa.timestamp 
                              WHERE nlp.cod_entidade = plnlp.cod_entidade                                                                    
                                AND nlp.cod_nota     = plnlp.cod_nota                                                                        
                                AND nlp.exercicio    = plnlp.exercicio_liquidacao                                                            
                                AND nlp.timestamp    = plnlp.timestamp                    
                                AND plnlp.exercicio = '".$this->getDado('exercicio')."'   
                                AND nlp.cod_entidade = tp.cod_entidade                                                                    
                                AND nlp.cod_nota     = tp.cod_nota                                                                        
                                AND nlp.exercicio    = tp.exercicio                                                            
                                AND nlp.timestamp    = tp.timestamp 
                                AND nlpa.cod_nota IS NULL
                           GROUP BY nlp.cod_entidade                                      
                                  , nlp.cod_nota                                          
                                  , nlp.exercicio                                         
                                  , nlpa.vl_anulado                                       
                                  , plnlp.cod_ordem                                       
                                  , plnlp.exercicio                                       
                                  , nlp.timestamp     
                                  , num_documento
                                  , tipo_documento_pagamento.descricao
                                  , ptdp.timestamp                                                   
                          ) as nota_liq_paga
                       ON pl.cod_nota     = nota_liq_paga.cod_nota                       
                      AND pl.cod_entidade = nota_liq_paga.cod_entidade                   
                      AND pl.exercicio    = nota_liq_paga.exercicio                      
                      AND pl.cod_ordem    = nota_liq_paga.cod_ordem                      
                      AND pl.exercicio_liquidacao = nota_liq_paga.exercicio_liquidacao   
                     JOIN empenho.empenho as em
                       ON nl.cod_empenho       = em.cod_empenho                          
                      AND nl.exercicio_empenho = em.exercicio                            
                      AND nl.cod_entidade      = em.cod_entidade                         
                      AND em.exercicio = '".$this->getDado('exercicio')."'                                  
                     JOIN empenho.pre_empenho as pe
                       ON em.exercicio       = pe.exercicio                              
                      AND em.cod_pre_empenho = pe.cod_pre_empenho                        
                      AND em.exercicio = '".$this->getDado('exercicio')."'    
                     JOIN ( SELECT sw_cgm.numcgm
			                     , nom_cgm
                                 , sw_cgm_pessoa_fisica.cpf AS documento
                                 , 1 AS tp_pessoa
                              FROM sw_cgm
                              JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                             UNION
                             SELECT sw_cgm.numcgm
                                  , nom_cgm
                                  , sw_cgm_pessoa_juridica.cnpj AS documento
                                  , 2 AS tp_pessoa
                              FROM sw_cgm
                              JOIN sw_cgm_pessoa_juridica
                                ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                        )  as cgm_pe
                      ON pe.cgm_beneficiario = cgm_pe.numcgm
               LEFT JOIN empenho.pre_empenho_despesa as ped
                      ON pe.cod_pre_empenho = ped.cod_pre_empenho                       
                     AND pe.exercicio       = ped.exercicio                             
               LEFT JOIN orcamento.despesa as de
                      ON ped.cod_despesa = de.cod_despesa
                     AND ped.exercicio   = de.exercicio                                 
                   WHERE nota_liq_paga.data_pagamento BETWEEN TO_DATE('".$this->getDado('dt_inicial')."' , 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."' , 'dd/mm/yyyy')    
                     AND opa.cod_ordem IS NULL
        ";
        return $stSql;
    }
    
    /**
        * Método Destruct
        * @access Private
    */
    public function __destruct(){}
}


?>
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
/*
    * Mapeamento tcmba.obra
    * Data de Criação   : 14/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: TTCMBAObra.class.php 63589 2015-09-14 19:18:58Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAObra extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.obra');
        $this->setCampoCod('cod_obra, cod_tipo, cod_entidade, exercicio');

        $this->AddCampo('cod_obra'              , 'integer' , true  , ''    , true , false);
        $this->AddCampo('cod_entidade'          , 'integer' , true  , ''    , true , true );
        $this->AddCampo('exercicio'             , 'varchar' , true  , '4'   , true , true );
        $this->AddCampo('cod_tipo'              , 'integer' , true  , ''    , true , true );
        $this->AddCampo('local'                 , 'varchar' , true  , '50'  , false, false);
        $this->AddCampo('cep'                   , 'varchar' , true  , '8'   , false, true );
        $this->AddCampo('cod_bairro'            , 'integer' , true  , ''    , false, true );
        $this->AddCampo('cod_uf'                , 'integer' , true  , ''    , false, true );
        $this->AddCampo('cod_municipio'         , 'integer' , true  , ''    , false, true );
        $this->AddCampo('cod_funcao'            , 'integer' , true  , ''    , false, true );
        $this->AddCampo('descricao'             , 'varchar' , true  , '255' , false, false);
        $this->AddCampo('vl_obra'               , 'numeric' , true  , '16,2', false, false);
        $this->AddCampo('data_cadastro'         , 'date'    , true  , ''    , false, false);
        $this->AddCampo('data_inicio'           , 'date'    , true  , ''    , false, false);
        $this->AddCampo('data_aceite'           , 'date'    , true  , ''    , false, false);
        $this->AddCampo('prazo'                 , 'integer' , true  , ''    , false, false);
        $this->AddCampo('data_recebimento'      , 'date'    , true  , ''    , false, false);
        $this->AddCampo('cod_licitacao'         , 'integer' , false , ''    , false, true );
        $this->AddCampo('cod_modalidade'        , 'integer' , false , ''    , false, true );
        $this->AddCampo('exercicio_licitacao'   , 'varchar' , false , '4'   , false, true );
    }
}

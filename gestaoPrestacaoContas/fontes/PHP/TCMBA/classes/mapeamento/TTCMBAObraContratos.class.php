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
    * Mapeamento tcmba.obra_contratos
    * Data de Criação   : 14/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: TTCMBAObraContratos.class.php 63589 2015-09-14 19:18:58Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAObraContratos extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcmba.obra_contratos');
        $this->setCampoCod('cod_obra, cod_tipo, cod_entidade, exercicio, cod_contratacao, numcgm');

        $this->AddCampo('cod_obra'              , 'integer' , true  , ''    , true , true );
        $this->AddCampo('cod_entidade'          , 'integer' , true  , ''    , true , true );
        $this->AddCampo('exercicio'             , 'varchar' , true  , '4'   , true , true );
        $this->AddCampo('cod_tipo'              , 'integer' , true  , ''    , true , true );
        $this->AddCampo('cod_contratacao'       , 'integer' , true  , ''    , true , true );
        $this->AddCampo('nro_instrumento'       , 'varchar' , false , '16'  , false, false);
        $this->AddCampo('nro_contrato'          , 'varchar' , false , '16'  , false, false);
        $this->AddCampo('nro_convenio'          , 'varchar' , false , '16'  , false, false);
        $this->AddCampo('nro_parceria'          , 'varchar' , false , '16'  , false, false);
        $this->AddCampo('numcgm'                , 'integer' , true  , ''    , true , true );
        $this->AddCampo('funcao_cgm'            , 'varchar' , true  , '50'  , false, false);
        $this->AddCampo('data_inicio'           , 'date'    , true  , ''    , false, false);
        $this->AddCampo('data_final'            , 'date'    , true  , ''    , false, false);
        $this->AddCampo('lotacao'               , 'varchar' , false , '50'  , false, false);
    }
}
